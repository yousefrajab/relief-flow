@php $showDispatch = $showDispatch ?? false; @endphp

<div class="space-y-3">
    @forelse($aidRequests as $aidRequest)
        <div class="bg-white border border-ink-100 rounded-2xl p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-ink-900">{{ $aidRequest->location }}</p>
                    <p class="text-[11px] text-ink-500 mt-0.5">
                        {{ __('Requested by') }} {{ $aidRequest->user->name }} · {{ $aidRequest->created_at->diffForHumans() }}
                    </p>
                </div>
                <x-status-badge :status="$aidRequest->status" />
            </div>

            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($aidRequest->requestItems as $requestItem)
                    <span class="text-[10px] font-semibold bg-ink-50 text-ink-600 border border-ink-100 rounded-full px-2.5 py-1">
                        {{ $requestItem->item->name }} × {{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}
                    </span>
                @endforeach
            </div>

            @if($aidRequest->notes)
                <p class="text-[11px] text-ink-400 mt-2">{{ $aidRequest->notes }}</p>
            @endif

            @if($aidRequest->status === 'rejected' && $aidRequest->rejection_reason)
                <p class="text-[11px] text-rose-600 mt-2 font-semibold">{{ __('Reason') }}: {{ $aidRequest->rejection_reason }}</p>
            @endif

            @if($aidRequest->shipment)
                <div class="mt-3 pt-3 border-t border-ink-100 flex flex-wrap items-center gap-3 text-[11px] text-ink-500">
                    <span>{{ __('Driver') }}: {{ $aidRequest->shipment->driver_name }} ({{ $aidRequest->shipment->driver_phone }})</span>
                    <a href="{{ route('shipments.print', $aidRequest->shipment) }}" target="_blank" class="font-bold text-field-600 hover:text-field-700">{{ __('Print Manifest') }}</a>
                </div>
            @endif

            @if($showDispatch && $aidRequest->status === 'pending')
                <div class="flex gap-2 pt-3 mt-3 border-t border-ink-100">
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'dispatch-{{ $aidRequest->id }}')" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-field-500 text-white hover:bg-field-600">{{ __('Dispatch') }}</button>
                    <button type="button" x-data x-on:click="$dispatch('open-modal', 'reject-{{ $aidRequest->id }}')" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-ink-100 text-ink-600 hover:bg-rose-50 hover:text-rose-600">{{ __('Reject') }}</button>
                </div>

                <x-modal name="dispatch-{{ $aidRequest->id }}" :title="__('Dispatch Shipment')">
                    <form method="POST" action="{{ route('aid-requests.dispatch', $aidRequest) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label :value="__('Dispatch from warehouse')" />
                            <select name="warehouse_id" required class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500">
                                <option value="" disabled selected>{{ __('Select warehouse') }}</option>
                                @foreach($warehouses as $warehouse)
                                    <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Driver name')" />
                            <x-text-input name="driver_name" required />
                        </div>
                        <div>
                            <x-input-label :value="__('Driver phone')" />
                            <x-text-input name="driver_phone" required />
                        </div>
                        <x-primary-button class="w-full justify-center">{{ __('Confirm Dispatch') }}</x-primary-button>
                    </form>
                </x-modal>

                <x-modal name="reject-{{ $aidRequest->id }}" :title="__('Reject Aid Request')">
                    <form method="POST" action="{{ route('aid-requests.reject', $aidRequest) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label :value="__('Reason for rejection')" />
                            <textarea name="rejection_reason" rows="3" required class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500"></textarea>
                        </div>
                        <x-danger-button class="w-full justify-center">{{ __('Confirm Rejection') }}</x-danger-button>
                    </form>
                </x-modal>
            @endif
        </div>
    @empty
        <p class="text-xs text-ink-400">{{ __('No aid requests yet.') }}</p>
    @endforelse
</div>
