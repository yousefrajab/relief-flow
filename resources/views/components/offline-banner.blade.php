<div
    x-data="{
        online: navigator.onLine,
        queueCount: 0,
        justSynced: null,
        init() {
            try {
                this.queueCount = JSON.parse(localStorage.getItem('reliefflow-offline-queue') || '[]').length;
            } catch (e) {
                this.queueCount = 0;
            }
            window.addEventListener('online', () => this.online = true);
            window.addEventListener('offline', () => this.online = false);
            window.addEventListener('reliefflow:queue-updated', (e) => this.queueCount = e.detail.count);
            window.addEventListener('reliefflow:queue-synced', (e) => {
                this.queueCount = e.detail.remaining;
                if (e.detail.synced > 0) {
                    this.justSynced = e.detail.synced;
                    setTimeout(() => this.justSynced = null, 6000);
                }
            });
        },
    }"
>
    <div x-show="!online || queueCount > 0" x-cloak class="bg-amber-alert-50 border border-amber-alert-200 text-amber-alert-800 text-xs font-bold rounded-2xl px-4 py-3 flex items-center gap-2 mb-4">
        <x-icon name="exclamation" class="w-4 h-4 shrink-0" />
        <span x-show="!online">{{ __("You're offline — previously visited pages are still available.") }}</span>
        <span x-show="online && queueCount > 0">{{ __('Syncing pending items…') }}</span>
        <span x-show="queueCount > 0" x-text="'(' + queueCount + ' ' + @js(__('pending sync')) + ')'"></span>
    </div>
    <div x-show="justSynced" x-cloak x-transition class="bg-field-50 border border-field-200 text-field-800 text-xs font-bold rounded-2xl px-4 py-3 mb-4">
        <span x-text="justSynced + ' ' + @js(__('queued item(s) synced successfully.'))"></span>
    </div>
</div>
