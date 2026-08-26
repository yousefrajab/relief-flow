<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-xl font-bold text-ink-900">{{ __('Administrator Overview') }}</h1>
            <p class="text-xs text-ink-500 mt-1">{{ __('Full visibility across warehouses, items, requests, and accounts.') }}</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Warehouses') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $totalWarehouses }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('Relief Items') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $totalItems }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-amber-alert-600 uppercase tracking-wide">{{ __('Pending Requests') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $pendingRequests }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-sky-600 uppercase tracking-wide">{{ __('Active Shipments') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $activeShipments }}</p>
            </div>
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

        <section id="warehouses" class="space-y-4 scroll-mt-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-ink-900">{{ __('Warehouses') }}</h2>
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-warehouse')" class="text-xs font-bold text-field-600 hover:text-field-700">+ {{ __('Add Warehouse') }}</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($warehouses as $warehouse)
                    <div class="bg-white border border-ink-100 rounded-2xl p-5 space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-bold text-ink-900">{{ $warehouse->name }}</p>
                                <p class="text-[11px] text-ink-500 mt-0.5">{{ $warehouse->location }}</p>
                            </div>
                            <x-status-badge :status="$warehouse->status" />
                        </div>
                        <p class="text-[11px] text-ink-500">{{ __('Capacity') }}: {{ number_format($warehouse->capacity) }} · {{ __('Stock lines') }}: {{ $warehouse->inventories_count }}</p>
                        <div class="flex gap-2 pt-2 border-t border-ink-100">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'edit-warehouse-{{ $warehouse->id }}')" class="text-[11px] font-bold text-ink-600 hover:text-ink-900">{{ __('Edit') }}</button>
                            <form method="POST" action="{{ route('warehouses.destroy', $warehouse) }}" onsubmit="return confirm('{{ __('Delete this warehouse?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </div>

                    <x-modal name="edit-warehouse-{{ $warehouse->id }}" :title="__('Edit Warehouse')">
                        <form method="POST" action="{{ route('warehouses.update', $warehouse) }}" class="space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <x-input-label :value="__('Name')" />
                                <x-text-input name="name" value="{{ $warehouse->name }}" required />
                            </div>
                            <div>
                                <x-input-label :value="__('Location')" />
                                <x-text-input name="location" value="{{ $warehouse->location }}" required />
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
                @empty
                    <p class="text-xs text-ink-400">{{ __('No warehouses yet.') }}</p>
                @endforelse
            </div>
        </section>

        <x-modal name="add-warehouse" :title="__('Add Warehouse')">
            <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label :value="__('Name')" />
                    <x-text-input name="name" required />
                </div>
                <div>
                    <x-input-label :value="__('Location')" />
                    <x-text-input name="location" required />
                </div>
                <div>
                    <x-input-label :value="__('Capacity')" />
                    <x-text-input type="number" name="capacity" required />
                </div>
                <x-primary-button class="w-full justify-center">{{ __('Add Warehouse') }}</x-primary-button>
            </form>
        </x-modal>

        <section id="items" class="space-y-4 scroll-mt-6">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-ink-900">{{ __('Relief Items') }}</h2>
                <button type="button" x-data x-on:click="$dispatch('open-modal', 'add-item')" class="text-xs font-bold text-field-600 hover:text-field-700">+ {{ __('Add Item') }}</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse($items as $item)
                    <div class="bg-white border border-ink-100 rounded-2xl p-5 space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <p class="text-sm font-bold text-ink-900">{{ $item->name }}</p>
                            <span class="text-[10px] font-bold bg-ink-100 text-ink-600 rounded-full px-2.5 py-1">{{ $item->category }}</span>
                        </div>
                        <p class="text-[11px] text-ink-500">{{ __('Unit') }}: {{ $item->unit }}</p>
                        @if($item->description)
                            <p class="text-[11px] text-ink-400">{{ $item->description }}</p>
                        @endif
                        <div class="flex gap-2 pt-2 border-t border-ink-100">
                            <button type="button" x-data x-on:click="$dispatch('open-modal', 'edit-item-{{ $item->id }}')" class="text-[11px] font-bold text-ink-600 hover:text-ink-900">{{ __('Edit') }}</button>
                            <form method="POST" action="{{ route('items.destroy', $item) }}" onsubmit="return confirm('{{ __('Delete this item?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Delete') }}</button>
                            </form>
                        </div>
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
                @empty
                    <p class="text-xs text-ink-400">{{ __('No relief items yet.') }}</p>
                @endforelse
            </div>
        </section>

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

        <section id="aid-requests" class="space-y-4 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Aid Requests') }}</h2>
            @include('partials.aid-request-list', ['aidRequests' => $aidRequests, 'showDispatch' => true])
        </section>

        <section id="accounts" class="space-y-6 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Accounts') }}</h2>

            @if($pendingUsers->isNotEmpty())
                <div class="space-y-3">
                    <p class="text-xs font-bold text-amber-alert-700">{{ __('Pending Approval') }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($pendingUsers as $pending)
                            <div class="bg-white border border-amber-alert-200 rounded-2xl p-4 flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-xs font-bold text-ink-900">{{ $pending->name }}</p>
                                    <p class="text-[11px] text-ink-500">{{ $pending->email }} · {{ $pending->role === 'depot_manager' ? __('Depot Manager') : __('Field Coordinator') }}</p>
                                </div>
                                <div class="flex gap-2 shrink-0">
                                    <form method="POST" action="{{ route('users.approve', $pending) }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-field-500 text-white hover:bg-field-600">{{ __('Approve') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('users.reject', $pending) }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-ink-100 text-ink-600 hover:bg-rose-50 hover:text-rose-600">{{ __('Reject') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="space-y-3">
                <p class="text-xs font-bold text-ink-700">{{ __('All Accounts') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-3">
                    @foreach($allUsers as $member)
                        <div class="bg-white border border-ink-100 rounded-2xl p-4">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-xs font-bold text-ink-900">{{ $member->name }}</p>
                                <x-status-badge :status="$member->status" />
                            </div>
                            <p class="text-[11px] text-ink-500 mt-1">{{ $member->email }}</p>
                            <p class="text-[11px] text-ink-400">{{ $member->role === 'depot_manager' ? __('Depot Manager') : __('Field Coordinator') }}</p>
                            @if($member->status === 'active')
                                <form method="POST" action="{{ route('users.reject', $member) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Suspend') }}</button>
                                </form>
                            @elseif($member->status === 'suspended')
                                <form method="POST" action="{{ route('users.approve', $member) }}" class="mt-2">
                                    @csrf
                                    <button type="submit" class="text-[11px] font-bold text-field-600 hover:text-field-700">{{ __('Reactivate') }}</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </div>
</x-app-layout>
