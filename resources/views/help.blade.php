<x-app-layout>
    <div class="space-y-6 max-w-2xl">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="help" class="w-5 h-5" /></div>
            <div>
                <h1 class="text-xl font-bold text-ink-900">{{ __('Help') }}</h1>
                <p class="text-xs text-ink-500 mt-0.5">{{ __('Quick answers for common questions about your role.') }}</p>
            </div>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl divide-y divide-ink-100" x-data="{ open: 1 }">
            @if(auth()->user()->role === 'coordinator')
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('How do I submit an aid request?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 1 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 1" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('Go to New Aid Request from your dashboard, enter the target location, optionally set it on the map, add one or more relief items with quantities, and submit. It will appear as Pending until a depot manager reviews it.') }}</p>
                </div>
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('How do I confirm a delivery?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 2 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 2" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('Open the shipment from your dashboard or the aid request page and use Confirm Delivery. Attaching a photo is optional but helps verification.') }}</p>
                </div>
            @elseif(auth()->user()->role === 'depot_manager')
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('How do I add stock?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 1 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 1" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('Go to Inventory and use Add Stock. Adding stock for a warehouse/item pair that already has some will increase the existing quantity.') }}</p>
                </div>
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('How does dispatch work?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 2 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 2" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('Open a pending aid request — warehouses are ranked by distance and whether they currently hold enough stock for every requested item. Pick one to dispatch, or reject the request with a reason if it cannot be fulfilled.') }}</p>
                </div>
            @else
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 1 ? null : 1" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('How do I approve a new account?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 1 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 1" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('Go to Accounts — pending registrations appear there with Approve/Reject actions.') }}</p>
                </div>
                <div class="p-5">
                    <button type="button" x-on:click="open = open === 2 ? null : 2" class="w-full flex items-center justify-between text-start gap-3">
                        <span class="text-xs font-bold text-ink-900">{{ __('What is the AI Impact Report?') }}</span>
                        <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 2 ? 'rotate-180' : ''" />
                    </button>
                    <p x-show="open === 2" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('A short AI-written summary of platform activity based only on real recorded statistics — deliveries, active shipments, and item categories distributed.') }}</p>
                </div>
            @endif

            <div class="p-5">
                <button type="button" x-on:click="open = open === 3 ? null : 3" class="w-full flex items-center justify-between text-start gap-3">
                    <span class="text-xs font-bold text-ink-900">{{ __('What does the priority badge mean?') }}</span>
                    <x-icon name="chevron-down" class="w-4 h-4 text-ink-400 shrink-0 transition-transform" x-bind:class="open === 3 ? 'rotate-180' : ''" />
                </button>
                <p x-show="open === 3" class="text-[11px] text-ink-500 mt-2 leading-relaxed">{{ __('New requests are automatically triaged by AI based on their notes and location. Critical and High priority requests are flagged so they can be reviewed first — this is advisory only and never blocks a request.') }}</p>
            </div>
        </div>
    </div>
</x-app-layout>
