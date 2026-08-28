<x-app-layout>
    <div class="space-y-6 max-w-4xl">
        <div>
            <a href="{{ route('aid-requests.show', $shipment->aidRequest) }}" class="text-[11px] font-bold text-ink-400 hover:text-ink-700">&larr; {{ __('Aid Request') }}</a>
            <div class="flex flex-wrap items-start justify-between gap-3 mt-1">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0"><x-icon name="truck" class="w-5 h-5" /></div>
                    <div>
                        <h1 class="text-xl font-bold text-ink-900">{{ $shipment->qr_code_token }}</h1>
                        <p class="text-xs text-ink-500 mt-0.5">{{ __('To') }} {{ $shipment->aidRequest->location }}</p>
                    </div>
                </div>
                <x-status-badge :status="$shipment->status" />
            </div>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Origin Warehouse') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->warehouse->name }}</p>
                </div>
                <div>
                    <p class="text-ink-400 font-semibold">{{ __('Driver') }}</p>
                    <p class="font-bold text-ink-900 mt-0.5">{{ $shipment->driver_name }} — {{ $shipment->driver_phone }}</p>
                </div>
            </div>

            <div>
                <p class="text-[11px] font-bold text-ink-500 mb-2">{{ __('Manifest Contents') }}</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($shipment->aidRequest->requestItems as $requestItem)
                        <span class="text-[10px] font-semibold bg-ink-50 text-ink-600 border border-ink-100 rounded-full px-2.5 py-1">
                            {{ $requestItem->item->name }} × {{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}
                        </span>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('shipments.print', $shipment) }}" target="_blank" class="inline-flex items-center gap-1.5 text-[11px] font-bold text-field-600 hover:text-field-700"><x-icon name="qr" class="w-3.5 h-3.5" /> {{ __('Print Manifest') }} &rarr;</a>
        </div>

        @if($shipment->picked_up_at)
            <div class="bg-violet-50 border border-violet-200 rounded-2xl p-6 space-y-3">
                <p class="text-xs font-bold text-violet-800">{{ __('Picked up from warehouse') }} {{ $shipment->picked_up_at->diffForHumans() }}</p>

                @if($shipment->pickup_photo_path)
                    <img src="{{ asset('storage/'.$shipment->pickup_photo_path) }}" class="rounded-xl border border-violet-200 max-h-64 object-cover">
                @endif

                @if($shipment->pickup_ai_verification_status)
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$shipment->pickup_ai_verification_status" />
                        @if($shipment->pickup_ai_verification_notes)
                            <p class="text-[11px] text-violet-700">{{ $shipment->pickup_ai_verification_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @elseif(auth()->user()->can('confirmPickup', $shipment))
            <div class="bg-white border border-ink-100 rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center"><x-icon name="camera" class="w-4 h-4" /></div>
                    <h2 class="text-sm font-bold text-ink-900">{{ __('Confirm Pickup from Warehouse') }}</h2>
                </div>
                <p class="text-[11px] text-ink-500">{{ __('Confirm you have collected this shipment from the warehouse. Optionally attach a photo of the load — our AI will do a quick plausibility check against the manifest.') }}</p>
                <form method="POST" action="{{ route('shipments.pickup', $shipment) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label :value="__('Pickup photo (optional)')" />
                        <input type="file" name="pickup_photo" accept="image/*" class="block w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-violet-50 file:text-violet-700 file:text-xs file:font-bold">
                        <x-input-error :messages="$errors->get('pickup_photo')" />
                    </div>
                    <x-primary-button class="w-full justify-center">{{ __('Confirm Pickup') }}</x-primary-button>
                </form>
            </div>
        @endif

        @if($shipment->status === 'delivered')
            <div class="bg-field-50 border border-field-200 rounded-2xl p-6 space-y-3">
                <p class="text-xs font-bold text-field-800">{{ __('Delivered') }} {{ $shipment->delivered_at->diffForHumans() }}</p>

                @if($shipment->delivery_photo_path)
                    <img src="{{ asset('storage/'.$shipment->delivery_photo_path) }}" class="rounded-xl border border-field-200 max-h-64 object-cover">
                @endif

                @if($shipment->ai_verification_status)
                    <div class="flex items-center gap-2">
                        <x-status-badge :status="$shipment->ai_verification_status" />
                        @if($shipment->ai_verification_notes)
                            <p class="text-[11px] text-field-700">{{ $shipment->ai_verification_notes }}</p>
                        @endif
                    </div>
                @endif
            </div>
        @elseif(auth()->user()->can('deliver', $shipment))
            <div class="bg-white border border-ink-100 rounded-2xl p-6 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center"><x-icon name="camera" class="w-4 h-4" /></div>
                    <h2 class="text-sm font-bold text-ink-900">{{ __('Confirm Delivery') }}</h2>
                </div>
                <p class="text-[11px] text-ink-500">{{ __('Optionally attach a photo of the received goods — our AI will do a quick plausibility check against the manifest.') }}</p>
                <form method="POST" action="{{ route('shipments.deliver', $shipment) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <x-input-label :value="__('Delivery photo (optional)')" />
                        <input type="file" name="delivery_photo" accept="image/*" class="block w-full text-xs text-ink-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:bg-field-50 file:text-field-700 file:text-xs file:font-bold">
                        <x-input-error :messages="$errors->get('delivery_photo')" />
                    </div>
                    <x-primary-button class="w-full justify-center">{{ __('Confirm Delivery') }}</x-primary-button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
