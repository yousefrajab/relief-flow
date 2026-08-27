<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Warehouses') }}</h1>
            @if(auth()->user()->role === 'admin')
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-warehouse')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="plus" class="w-4 h-4" /> {{ __('Add Warehouse') }}</button>
            @endif
        </div>

        <div
            x-data="{
                search: '',
                status: '',
                visible(name, location, status) {
                    const s = this.search.toLowerCase();
                    const matchesSearch = !s || name.toLowerCase().includes(s) || location.toLowerCase().includes(s);
                    const matchesStatus = !this.status || this.status === status;
                    return matchesSearch && matchesStatus;
                }
            }"
            class="space-y-4"
        >
            <div class="bg-white border border-ink-100 rounded-2xl p-4 flex flex-wrap items-end gap-3">
                <div class="flex-grow min-w-[180px]">
                    <x-input-label :value="__('Search')" class="mb-1" />
                    <div class="relative">
                        <x-icon name="search" class="w-4 h-4 text-ink-300 absolute top-1/2 -translate-y-1/2 start-3 pointer-events-none" />
                        <input type="text" x-model="search" placeholder="{{ __('Name or location') }}" class="w-full ps-9 rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                    </div>
                </div>
                <div>
                    <x-input-label :value="__('Status')" class="mb-1" />
                    <select x-model="status" class="rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                        <option value="">{{ __('All statuses') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($warehouses as $warehouse)
                    <a href="{{ route('warehouses.show', $warehouse) }}"
                       x-show="visible(@js($warehouse->name), @js($warehouse->location), @js($warehouse->status))"
                       class="bg-white border border-ink-100 rounded-2xl p-5 space-y-3 hover:border-field-300 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="warehouse" class="w-4.5 h-4.5" /></div>
                                <div>
                                    <p class="text-sm font-bold text-ink-900">{{ $warehouse->name }}</p>
                                    <p class="text-[11px] text-ink-500 mt-0.5">{{ $warehouse->location }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$warehouse->status" />
                        </div>
                        <p class="text-[11px] text-ink-500">{{ __('Capacity') }}: {{ number_format($warehouse->capacity) }} · {{ __('Stock lines') }}: {{ $warehouse->inventories_count }}</p>
                    </a>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-3"><x-icon name="warehouse" class="w-7 h-7" /></div>
                        <p class="text-xs font-bold text-ink-500">{{ __('No warehouses yet.') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <x-modal name="add-warehouse" :title="__('Add Warehouse')">
            <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-4" x-data="{ location: '' }" x-on:location-picked.window="location = $event.detail">
                @csrf
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" required />
                </div>
                <div>
                    <x-input-label :value="__('Location description')" />
                    <x-text-input name="location" x-model="location" required />
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
