const STORAGE_KEY = 'reliefflow-offline-queue';

function readQueue() {
    try {
        return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
    } catch (e) {
        return [];
    }
}

function writeQueue(queue) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
    window.dispatchEvent(new CustomEvent('reliefflow:queue-updated', { detail: { count: queue.length } }));
}

// Queues a native <form> submission for background sync once connectivity
// returns. Captures every field via FormData (including the CSRF token
// already present as a hidden input), so the request Laravel eventually
// receives is indistinguishable from a normal same-page submission.
export function queueFormSubmission(form, label) {
    const entries = Array.from(new FormData(form).entries()).filter(([, value]) => typeof value === 'string');
    const queue = readQueue();

    queue.push({
        id: `${Date.now()}-${Math.random().toString(36).slice(2)}`,
        url: form.action,
        entries,
        label: label || '',
        queuedAt: Date.now(),
    });

    writeQueue(queue);
}

export function queueLength() {
    return readQueue().length;
}

// Probes connectivity with a cheap, side-effect-free GET, then submits the
// form NATIVELY (form.submit()) rather than via fetch. This is deliberate:
// an early version POSTed the form itself over fetch and navigated to
// response.url on success, but fetch follows redirects internally — that
// silently consumes Laravel's one-time flashed validation errors during the
// followed redirect, so the page this code then navigated to (a second,
// separate request) rendered with no errors at all, even though validation
// had genuinely failed. Native form.submit() has none of that problem: it's
// the same single browser-driven request/redirect cycle as before this
// feature existed, so success, validation errors, and flash messages all
// behave exactly as they always did. The probe exists only to distinguish
// "genuinely offline" from "online" beforehand, since navigator.onLine
// alone isn't reliable (confirmed against a simulated offline network).
export async function submitFormOrQueue(form, label) {
    try {
        await fetch('/notifications/poll', { headers: { 'Accept': 'application/json' }, cache: 'no-store' });
        form.submit();
    } catch (e) {
        queueFormSubmission(form, label);
        form.dispatchEvent(new CustomEvent('reliefflow:queued', { bubbles: true }));
    }
}

let syncing = false;

// Flushes every queued submission. An entry is removed once ANY server
// response comes back (success, validation error, etc.) — only a genuine
// network failure keeps it queued for the next attempt. A 419 (expired
// session/CSRF) is the one exception: dropping it would silently lose the
// user's submission, so it stays queued and is surfaced instead.
export async function syncQueue() {
    if (syncing) return;
    syncing = true;

    const queue = readQueue();
    const remaining = [];
    let syncedCount = 0;
    let expiredCount = 0;

    for (const item of queue) {
        try {
            const body = new URLSearchParams(item.entries);
            const response = await fetch(item.url, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body,
            });

            if (response.status === 419) {
                remaining.push(item);
                expiredCount++;
            } else {
                syncedCount++;
            }
        } catch (e) {
            remaining.push(item);
        }
    }

    writeQueue(remaining);
    syncing = false;

    if (syncedCount > 0 || expiredCount > 0) {
        window.dispatchEvent(new CustomEvent('reliefflow:queue-synced', {
            detail: { synced: syncedCount, expired: expiredCount, remaining: remaining.length },
        }));
    }
}

window.addEventListener('online', syncQueue);
document.addEventListener('DOMContentLoaded', syncQueue);
