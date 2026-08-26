@props(['name', 'title' => ''])

<div x-data="{ open: false }"
     x-on:open-modal.window="$event.detail === '{{ $name }}' && (open = true)"
     x-on:close-modal.window="open = false"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none;">
    <div x-show="open" x-transition.opacity x-on:click="open = false" class="absolute inset-0 bg-ink-950/60 backdrop-blur-sm"></div>

    <div x-show="open" x-transition x-on:click.away="open = false" class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
            <h3 class="text-sm font-bold text-ink-900">{{ $title }}</h3>
            <button type="button" x-on:click="open = false" class="text-ink-400 hover:text-ink-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
