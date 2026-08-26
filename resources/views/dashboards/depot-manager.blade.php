<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-xl font-bold text-ink-900">{{ __('Depot Operations') }}</h1>
            <p class="text-xs text-ink-500 mt-1">{{ __('Manage stock levels and dispatch shipments to the field.') }}</p>
        </div>

        @if($lowStockAlerts->isNotEmpty())
            <div class="bg-amber-alert-50 border border-amber-alert-200 rounded-2xl p-4">
                <p class="text-xs font-bold text-amber-alert-800 mb-2">{{ __('Low stock alert') }}</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($lowStockAlerts as $alert)
                        <span class="text-[10px] font-semibold bg-white text-amber-alert-700 border border-amber-alert-200 rounded-full px-2.5 py-1">
                            {{ $alert->item->name }} · {{ $alert->warehouse->name }} · {{ number_format($alert->quantity) }} {{ $alert->item->unit }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <section id="inventory" class="space-y-4 scroll-mt-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-ink-900">{{ __('Inventory') }}</h2>
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-stock')" class="text-xs font-bold text-field-600 hover:text-field-700">+ {{ __('Add Stock') }}</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($inventories as $inventory)
                    <div class="bg-white border border-ink-100 rounded-2xl p-5">
                        <p class="text-sm font-bold text-ink-900">{{ $inventory->item->name }}</p>
                        <p class="text-[11px] text-ink-500 mt-0.5">{{ $inventory->warehouse->name }}</p>
                        <p class="text-lg font-extrabold {{ $inventory->quantity < 1000 ? 'text-amber-alert-600' : 'text-field-600' }} mt-2">
                            {{ number_format($inventory->quantity) }} <span class="text-xs font-semibold text-ink-400">{{ $inventory->item->unit }}</span>
                        </p>
                    </div>
                @empty
                    <p class="text-xs text-ink-400">{{ __('No stock recorded yet.') }}</p>
                @endforelse
            </div>
        </section>

        <x-modal name="add-stock" :title="__('Add Stock')">
            <form method="POST" action="{{ route('inventory.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label :value="__('Warehouse')" />
                    <select name="warehouse_id" required class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                        <option value="" disabled selected>{{ __('Select warehouse') }}</option>
                        @foreach($warehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Relief item')" />
                    <select name="item_id" required class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                        <option value="" disabled selected>{{ __('Select item') }}</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label :value="__('Quantity to add')" />
                    <x-text-input type="number" name="quantity" min="1" required />
                </div>
                <x-primary-button class="w-full justify-center">{{ __('Add Stock') }}</x-primary-button>
            </form>
        </x-modal>

        <section id="pending-requests" class="space-y-4 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Pending Requests') }}</h2>
            @include('partials.aid-request-list', ['aidRequests' => $pendingRequests, 'showDispatch' => true])
        </section>

        <section id="shipments" class="space-y-4 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Dispatched Shipments') }}</h2>
            <div class="space-y-3">
                @forelse($dispatchedShipments as $shipment)
                    <div class="bg-white border border-ink-100 rounded-2xl p-5 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                            <p class="text-[11px] text-ink-500 mt-0.5">{{ __('Driver') }}: {{ $shipment->driver_name }} ({{ $shipment->driver_phone }}) · {{ __('From') }} {{ $shipment->warehouse->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <x-status-badge :status="$shipment->status" />
                            <a href="{{ route('shipments.print', $shipment) }}" target="_blank" class="text-[11px] font-bold text-field-600 hover:text-field-700">{{ __('Print Manifest') }}</a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-ink-400">{{ __('No active shipments.') }}</p>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
