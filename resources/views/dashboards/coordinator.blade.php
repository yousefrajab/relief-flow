<x-app-layout>
    <div class="space-y-8">
        <div>
            <h1 class="text-xl font-bold text-ink-900">{{ __('Field Coordination') }}</h1>
            <p class="text-xs text-ink-500 mt-1">{{ __('Submit aid requests and confirm deliveries in the field.') }}</p>
        </div>

        @if($myShipmentsAwaitingDelivery->isNotEmpty())
            <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4 space-y-3">
                <p class="text-xs font-bold text-sky-800">{{ __('Shipments awaiting your confirmation') }}</p>
                @foreach($myShipmentsAwaitingDelivery as $shipment)
                    <div class="bg-white border border-sky-100 rounded-xl p-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                            <p class="text-[11px] text-ink-500">{{ __('Driver') }}: {{ $shipment->driver_name }} ({{ $shipment->driver_phone }})</p>
                        </div>
                        <form method="POST" action="{{ route('shipments.deliver', $shipment) }}">
                            @csrf
                            <button type="submit" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-field-500 text-white hover:bg-field-600">{{ __('Confirm Delivery') }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <section id="new-request" class="space-y-4 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('New Aid Request') }}</h2>

            <div class="bg-white border border-ink-100 rounded-2xl p-6"
                 x-data="{ rows: [{ item_id: '', quantity: 1 }] }">
                <form method="POST" action="{{ route('aid-requests.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label :value="__('Target distribution location')" />
                        <x-text-input name="location" required />
                    </div>

                    <div>
                        <x-input-label :value="__('Notes (optional)')" />
                        <textarea name="notes" rows="2" class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500"></textarea>
                    </div>

                    <div class="space-y-3">
                        <x-input-label :value="__('Requested items')" />
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="flex gap-2 items-start">
                                <select :name="`items[${index}][item_id]`" x-model="row.item_id" required class="flex-grow rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                                    <option value="" disabled selected>{{ __('Select item') }}</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                    @endforeach
                                </select>
                                <input type="number" :name="`items[${index}][quantity]`" x-model="row.quantity" min="1" required class="w-24 rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                                <button type="button" x-on:click="rows.length > 1 && rows.splice(index, 1)" class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl bg-ink-100 text-ink-500 hover:bg-rose-50 hover:text-rose-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                </button>
                            </div>
                        </template>
                        <button type="button" x-on:click="rows.push({ item_id: '', quantity: 1 })" class="text-[11px] font-bold text-field-600 hover:text-field-700">+ {{ __('Add another item') }}</button>
                    </div>

                    <x-primary-button class="w-full justify-center">{{ __('Submit Request') }}</x-primary-button>
                </form>
            </div>
        </section>

        <section id="my-requests" class="space-y-4 scroll-mt-6">
            <h2 class="text-sm font-bold text-ink-900">{{ __('My Requests') }}</h2>
            @include('partials.aid-request-list', ['aidRequests' => $myRequests, 'showDispatch' => false])
        </section>
    </div>
</x-app-layout>
