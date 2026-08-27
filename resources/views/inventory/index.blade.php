<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Inventory') }}</h1>
            <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-stock')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="plus" class="w-4 h-4" /> {{ __('Add Stock') }}</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($inventories as $inventory)
                <div class="bg-white border border-ink-100 rounded-2xl p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-2.5 mb-1">
                        <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="inventory" class="w-4 h-4" /></div>
                        <p class="text-sm font-bold text-ink-900">{{ $inventory->item->name }}</p>
                    </div>
                    <p class="text-[11px] text-ink-500 mt-0.5">{{ $inventory->warehouse->name }}</p>
                    <p class="text-lg font-extrabold {{ $inventory->quantity < 1000 ? 'text-amber-alert-600' : 'text-field-600' }} mt-2">
                        {{ number_format($inventory->quantity) }} <span class="text-xs font-semibold text-ink-400">{{ $inventory->item->unit }}</span>
                    </p>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-3"><x-icon name="inventory" class="w-7 h-7" /></div>
                    <p class="text-xs font-bold text-ink-500">{{ __('No stock recorded yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

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
</x-app-layout>
