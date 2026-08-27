<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Warehouses') }}</h1>
            @if(auth()->user()->role === 'admin')
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-warehouse')" class="px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold">+ {{ __('Add Warehouse') }}</button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($warehouses as $warehouse)
                <a href="{{ route('warehouses.show', $warehouse) }}" class="bg-white border border-ink-100 rounded-2xl p-5 space-y-3 hover:border-field-300 transition-colors">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-sm font-bold text-ink-900">{{ $warehouse->name }}</p>
                            <p class="text-[11px] text-ink-500 mt-0.5">{{ $warehouse->location }}</p>
                        </div>
                        <x-status-badge :status="$warehouse->status" />
                    </div>
                    <p class="text-[11px] text-ink-500">{{ __('Capacity') }}: {{ number_format($warehouse->capacity) }} · {{ __('Stock lines') }}: {{ $warehouse->inventories_count }}</p>
                </a>
            @empty
                <p class="text-xs text-ink-400">{{ __('No warehouses yet.') }}</p>
            @endforelse
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <x-modal name="add-warehouse" :title="__('Add Warehouse')">
            <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" required />
                </div>
                <div>
                    <x-input-label :value="__('Location description')" />
                    <x-text-input name="location" required />
                </div>
                <div>
                    <x-input-label :value="__('Map location')" />
                    <x-location-picker />
                </div>
                <div>
                    <x-input-label :value="__('Capacity')" />
                    <x-text-input type="number" name="capacity" required />
                </div>
                <x-primary-button class="w-full justify-center">{{ __('Add Warehouse') }}</x-primary-button>
            </form>
        </x-modal>
    @endif
</x-app-layout>
