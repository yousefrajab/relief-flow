<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-xl font-bold text-ink-900">{{ __('Administrator Overview') }}</h1>
            <p class="text-xs text-ink-500 mt-1">{{ __('Full visibility across warehouses, items, requests, and accounts.') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('warehouses.index') }}" class="bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 transition-colors">
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Warehouses') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $totalWarehouses }}</p>
            </a>
            <a href="{{ route('items.index') }}" class="bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 transition-colors">
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Relief Items') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $totalItems }}</p>
            </a>
            <a href="{{ route('aid-requests.index') }}" class="bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 transition-colors">
                <p class="text-[10px] font-bold text-amber-alert-600 uppercase tracking-wide">{{ __('Pending Requests') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $pendingRequests }}</p>
            </a>
            <a href="{{ route('aid-requests.index') }}" class="bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 transition-colors">
                <p class="text-[10px] font-bold text-sky-600 uppercase tracking-wide">{{ __('Active Shipments') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $activeShipments }}</p>
            </a>
        </div>

        @if($pendingUsersCount > 0)
            <a href="{{ route('admin.users') }}" class="block bg-amber-alert-50 border border-amber-alert-200 rounded-2xl p-4 hover:border-amber-alert-400 transition-colors">
                <p class="text-xs font-bold text-amber-alert-800">{{ __(':count accounts are waiting for your approval.', ['count' => $pendingUsersCount]) }}</p>
            </a>
        @endif

        @if($lowStockAlerts->isNotEmpty())
            <div class="bg-amber-alert-50 border border-amber-alert-200 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-alert-800 mb-2">{{ __('Low stock alert') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($lowStockAlerts as $alert)
                        <a href="{{ route('inventory.index') }}" class="text-[10px] font-semibold bg-white text-amber-alert-700 border border-amber-alert-200 rounded-full px-2.5 py-1 hover:border-amber-alert-400">
                            {{ $alert->item->name }} · {{ $alert->warehouse->name }} · {{ number_format($alert->quantity) }} {{ $alert->item->unit }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-ink-900">{{ __('Recent Aid Requests') }}</h2>
                <a href="{{ route('aid-requests.index') }}" class="text-xs font-bold text-field-600 hover:text-field-700">{{ __('View all') }} &rarr;</a>
            </div>
            @include('partials.aid-request-list', ['aidRequests' => $recentRequests])
        </section>
    </div>
</x-app-layout>
