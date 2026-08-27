<x-app-layout>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-ink-900">{{ __('Relief Items') }}</h1>
            @if(auth()->user()->role === 'admin')
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-item')" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold"><x-icon name="plus" class="w-4 h-4" /> {{ __('Add Item') }}</button>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($items as $item)
                <div class="bg-white border border-ink-100 rounded-2xl p-5 space-y-2 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="box" class="w-4 h-4" /></div>
                            <p class="text-sm font-bold text-ink-900">{{ $item->name }}</p>
                        </div>
                        <span class="text-[10px] font-bold bg-ink-100 text-ink-600 rounded-full px-2.5 py-1">{{ $item->category }}</span>
                    </div>
                    <p class="text-[11px] text-ink-500">{{ __('Unit') }}: {{ $item->unit }} · {{ __('Stocked in') }} {{ $item->inventories_count }} {{ __('warehouse(s)') }}</p>
                    @if($item->description)
                        <p class="text-[11px] text-ink-400">{{ $item->description }}</p>
                    @endif
                    @if(auth()->user()->role === 'admin')
                        <div class="flex gap-2 pt-2 border-t border-ink-100">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'edit-item-{{ $item->id }}')" class="text-[11px] font-bold text-ink-600 hover:text-ink-900">{{ __('Edit') }}</button>
                            <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Delete') }}</button>
                            </form>
                        </div>

                        <x-modal name="edit-item-{{ $item->id }}" :title="__('Edit Item')">
                            <form method="POST" action="{{ route('items.update', $item) }}" class="space-y-4">
                                @csrf @method('PUT')
                                <div>
                                    <x-input-label :value="__('Name')" />
                                    <x-text-input name="name" value="{{ $item->name }}" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Category')" />
                                    <x-text-input name="category" value="{{ $item->category }}" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Unit')" />
                                    <x-text-input name="unit" value="{{ $item->unit }}" required />
                                </div>
                                <div>
                                    <x-input-label :value="__('Description')" />
                                    <textarea name="description" rows="2" class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">{{ $item->description }}</textarea>
                                </div>
                                <x-primary-button class="w-full justify-center">{{ __('Save changes') }}</x-primary-button>
                            </form>
                        </x-modal>
                    @endif
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-3"><x-icon name="box" class="w-7 h-7" /></div>
                    <p class="text-xs font-bold text-ink-500">{{ __('No relief items yet.') }}</p>
                </div>
            @endforelse
        </div>
    </div>

    @if(auth()->user()->role === 'admin')
        <x-modal name="add-item" :title="__('Add Relief Item')">
            <form method="POST" action="{{ route('items.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" required />
                </div>
                <div>
                    <x-input-label :value="__('Category')" />
                    <x-text-input name="category" required placeholder="Food, Medical, Hygiene, Shelter..." />
                </div>
                <div>
                    <x-input-label :value="__('Unit')" />
                    <x-text-input name="unit" required placeholder="box, kit, kg..." />
                </div>
                <div>
                    <x-input-label :value="__('Description')" />
                    <textarea name="description" rows="2" class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500"></textarea>
                </div>
                <x-primary-button class="w-full justify-center">{{ __('Add Item') }}</x-primary-button>
            </form>
        </x-modal>
    @endif
</x-app-layout>
