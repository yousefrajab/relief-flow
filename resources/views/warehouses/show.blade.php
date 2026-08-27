<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <a href="{{ route('warehouses.index') }}" class="text-[11px] font-bold text-ink-400 hover:text-ink-700">&larr; {{ __('Warehouses') }}</a>
                <div class="flex items-start gap-3 mt-1">
                    <div class="w-11 h-11 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="warehouse" class="w-5 h-5" /></div>
                    <div>
                        <h1 class="text-xl font-bold text-ink-900">{{ $warehouse->name }}</h1>
                        <p class="text-xs text-ink-500 mt-0.5">{{ $warehouse->location }}</p>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <x-status-badge :status="$warehouse->status" />
                @if(auth()->user()->role === 'admin')
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'edit-warehouse')" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-ink-100 text-ink-600 hover:bg-ink-200">{{ __('Edit') }}</button>
                @endif
            </div>
        </div>

        @if($warehouse->latitude && $warehouse->longitude)
            <div id="warehouse-map" style="height: 280px;" class="rounded-2xl border border-ink-200"></div>
        @endif

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center mb-3"><x-icon name="inventory" class="w-4.5 h-4.5" /></div>
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Capacity') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ number_format($warehouse->capacity) }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center mb-3"><x-icon name="box" class="w-4.5 h-4.5" /></div>
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Stock lines') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $warehouse->inventories->count() }}</p>
            </div>
        </div>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Current Stock') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                @forelse($warehouse->inventories as $inventory)
                    <div class="bg-white border border-ink-100 rounded-2xl p-4">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-7 h-7 rounded-lg bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="box" class="w-3.5 h-3.5" /></div>
                            <p class="text-xs font-bold text-ink-900">{{ $inventory->item->name }}</p>
                        </div>
                        <p class="text-lg font-extrabold {{ $inventory->quantity < 1000 ? 'text-amber-alert-600' : 'text-field-600' }} mt-1">
                            {{ number_format($inventory->quantity) }} <span class="text-xs font-semibold text-ink-400">{{ $inventory->item->unit }}</span>
                        </p>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-2"><x-icon name="box" class="w-6 h-6" /></div>
                        <p class="text-xs font-bold text-ink-500">{{ __('No stock recorded yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="space-y-3">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Shipment History') }}</h2>
            <div class="space-y-2">
                @forelse($warehouse->shipments as $shipment)
                    <a href="{{ route('shipments.show', $shipment) }}" class="flex items-center justify-between gap-3 bg-white border border-ink-100 rounded-2xl p-4 hover:border-field-300 hover:shadow-md transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0"><x-icon name="truck" class="w-4.5 h-4.5" /></div>
                            <div>
                                <p class="text-xs font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                                <p class="text-[11px] text-ink-500">{{ $shipment->qr_code_token }}</p>
                            </div>
                        </div>
                        <x-status-badge :status="$shipment->status" />
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-2"><x-icon name="truck" class="w-6 h-6" /></div>
                        <p class="text-xs font-bold text-ink-500">{{ __('No shipments dispatched from this warehouse yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if(auth()->user()->role === 'admin')
            <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" onsubmit="return confirm('{{ __('Delete this warehouse?') }}')">
                @csrf @method('DELETE')
                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Delete this warehouse') }}</button>
            </form>
        @endif
    </div>

    @if(auth()->user()->role === 'admin')
        <x-modal name="edit-warehouse" :title="__('Edit Warehouse')">
            <form method="POST" action="{{ route('warehouses.update', $warehouse) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" value="{{ $warehouse->name }}" required />
                </div>
                <div>
                    <x-input-label :value="__('Location description')" />
                    <x-text-input name="location" value="{{ $warehouse->location }}" required />
                </div>
                <div>
                    <x-input-label :value="__('Map location')" />
                    <x-location-picker :lat="$warehouse->latitude ?? 31.5" :lng="$warehouse->longitude ?? 34.4667" />
                </div>
                <div>
                    <x-input-label :value="__('Capacity')" />
                    <x-text-input type="number" name="capacity" value="{{ $warehouse->capacity }}" required />
                </div>
                <div>
                    <x-input-label :value="__('Status')" />
                    <select name="status" class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                        <option value="active" {{ $warehouse->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                        <option value="inactive" {{ $warehouse->status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                    </select>
                </div>
                <x-primary-button class="w-full justify-center">{{ __('Save changes') }}</x-primary-button>
            </form>
        </x-modal>
    @endif

    @if($warehouse->latitude && $warehouse->longitude)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var map = L.map('warehouse-map').setView([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);
                L.marker([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}]).addTo(map).bindPopup(@json($warehouse->name));
            });
        </script>
    @endif
</x-app-layout>
