<x-app-layout>
    <div class="space-y-6 max-w-4xl">
        <div>
            <a href="{{ route('aid-requests.index') }}" class="text-[11px] font-bold text-ink-400 hover:text-ink-700">&larr; {{ __('Aid Requests') }}</a>
            <div class="flex flex-wrap items-start justify-between gap-3 mt-1">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="clipboard" class="w-5 h-5" /></div>
                    <div>
                        <h1 class="text-xl font-bold text-ink-900">{{ $aidRequest->location }}</h1>
                        <p class="text-xs text-ink-500 mt-0.5">{{ __('Requested by') }} {{ $aidRequest->user->name }} · {{ $aidRequest->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($aidRequest->priority === 'critical')
                        <x-status-badge status="critical" />
                    @elseif($aidRequest->priority === 'high')
                        <x-status-badge status="high" />
                    @endif
                    <x-status-badge :status="$aidRequest->status" />
                </div>
            </div>
        </div>

        <div class="bg-white border border-ink-100 rounded-2xl p-6 space-y-5">
            <div class="flex items-center gap-3 text-[11px] font-bold">
                <div class="flex items-center gap-1.5 {{ in_array($aidRequest->status, ['pending', 'dispatched', 'delivered']) ? 'text-field-600' : 'text-ink-300' }}">
                    <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Submitted') }}
                </div>
                <div class="flex-grow h-px bg-ink-100"></div>
                @if($aidRequest->status === 'rejected')
                    <div class="flex items-center gap-1.5 text-rose-600">
                        <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Rejected') }}
                    </div>
                @else
                    <div class="flex items-center gap-1.5 {{ in_array($aidRequest->status, ['dispatched', 'delivered']) ? 'text-field-600' : 'text-ink-300' }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Dispatched') }}
                    </div>
                    <div class="flex-grow h-px bg-ink-100"></div>
                    <div class="flex items-center gap-1.5 {{ $aidRequest->status === 'delivered' ? 'text-field-600' : 'text-ink-300' }}">
                        <span class="w-2 h-2 rounded-full bg-current"></span> {{ __('Delivered') }}
                    </div>
                @endif
            </div>

            <div>
                <p class="text-[11px] font-bold text-ink-500 mb-2">{{ __('Requested items') }}</p>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($aidRequest->requestItems as $requestItem)
                        <span class="text-[10px] font-semibold bg-ink-50 text-ink-600 border border-ink-100 rounded-full px-2.5 py-1">
                            {{ $requestItem->item->name }} × {{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if($aidRequest->notes)
                <div>
                    <p class="text-[11px] font-bold text-ink-500 mb-1">{{ __('Notes') }}</p>
                    <p class="text-xs text-ink-700">{{ $aidRequest->notes }}</p>
                </div>
            @endif

            @if($aidRequest->status === 'rejected' && $aidRequest->rejection_reason)
                <div class="bg-rose-50 border border-rose-200 rounded-xl p-3">
                    <p class="text-[11px] font-bold text-rose-700">{{ __('Reason for rejection') }}</p>
                    <p class="text-xs text-rose-700 mt-0.5">{{ $aidRequest->rejection_reason }}</p>
                </div>
            @endif

            @if($aidRequest->shipment)
                <a href="{{ route('shipments.show', $aidRequest->shipment) }}" class="flex items-center justify-between bg-sky-50 border border-sky-200 rounded-xl p-3 hover:border-sky-400 transition-colors">
                    <div>
                        <p class="text-[11px] font-bold text-sky-800">{{ __('Shipment') }} {{ $aidRequest->shipment->qr_code_token }}</p>
                        <p class="text-[11px] text-sky-600">{{ __('Driver') }}: {{ $aidRequest->shipment->driver_name }}</p>
                    </div>
                    <span class="text-[11px] font-bold text-sky-700">{{ __('View') }} &rarr;</span>
                </a>
            @endif
        </div>

        <section class="space-y-3">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-field-50 text-field-600 flex items-center justify-center"><x-icon name="clipboard" class="w-4 h-4" /></div>
                <h2 class="text-sm font-bold text-ink-900">{{ __('Activity Log') }}</h2>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl divide-y divide-ink-50">
                @forelse($aidRequest->activities as $activity)
                    <div class="p-4 flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs text-ink-800">
                                <span class="font-bold">{{ $activity->user->name }}</span>
                                {{ match($activity->action) {
                                    'submitted' => __('submitted this request'),
                                    'rejected' => __('rejected this request'),
                                    'dispatched' => __('dispatched a shipment'),
                                    'delivered' => __('confirmed delivery'),
                                    default => $activity->action,
                                } }}
                            </p>
                            @if($activity->notes)
                                <p class="text-[11px] text-ink-500 mt-1">{{ $activity->notes }}</p>
                            @endif
                        </div>
                        <p class="text-[10px] text-ink-400 shrink-0">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p class="p-4 text-[11px] text-ink-400">{{ __('No activity recorded yet.') }}</p>
                @endforelse
            </div>
        </section>

        @can('reject', $aidRequest)
            <section class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center"><x-icon name="route" class="w-4 h-4" /></div>
                    <h2 class="text-sm font-bold text-ink-900">{{ __('Dispatch this request') }}</h2>
                </div>
                <p class="text-[11px] text-ink-500">{{ __('Warehouses ranked by distance and whether they currently hold enough stock for every item in this request.') }}</p>

                <div class="space-y-2">
                    @foreach($matches as $match)
                        <div class="bg-white border {{ $match['can_fulfill'] ? 'border-ink-100' : 'border-ink-100 opacity-60' }} rounded-2xl p-4 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-bold text-ink-900">{{ $match['warehouse']->name }}</p>
                                <p class="text-[11px] text-ink-500">
                                    {{ $match['distance_km'] !== null ? __(':km km away', ['km' => $match['distance_km']]) : __('Distance unknown') }}
                                    @if(! $match['can_fulfill'])
                                        · {{ __('Short on') }}: {{ implode(', ', $match['shortfalls']) }}
                                    @endif
                                </p>
                            </div>
                            @if($match['can_fulfill'])
                                <button type="button" x-data x-on:click="$dispatch('open-modal', 'dispatch-{{ $match['warehouse']->id }}')" class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-field-500 text-white hover:bg-field-600">{{ __('Dispatch from here') }}</button>
                            @else
                                <span class="text-[11px] font-bold px-3 py-1.5 rounded-lg bg-ink-100 text-ink-400">{{ __('Insufficient stock') }}</span>
                            @endif
                        </div>

                        @if($match['can_fulfill'])
                            <x-modal name="dispatch-{{ $match['warehouse']->id }}" :title="__('Dispatch from :warehouse', ['warehouse' => $match['warehouse']->name])">
                                <form method="POST" action="{{ route('aid-requests.dispatch', $aidRequest) }}" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="warehouse_id" value="{{ $match['warehouse']->id }}">
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
                        @endif
                    @endforeach
                </div>

                <button type="button" x-data x-on:click="$dispatch('open-modal', 'reject-request')" class="text-[11px] font-bold text-rose-600 hover:text-rose-700">{{ __('Reject this request instead') }}</button>

                <x-modal name="reject-request" :title="__('Reject Aid Request')">
                    <form method="POST" action="{{ route('aid-requests.reject', $aidRequest) }}" class="space-y-4">
                        @csrf
                        <div>
                            <x-input-label :value="__('Reason for rejection')" />
                            <textarea name="rejection_reason" rows="3" required class="block w-full rounded-xl border-ink-200 text-sm focus:border-field-500 focus:ring-field-500"></textarea>
                        </div>
                        <x-danger-button class="w-full justify-center">{{ __('Confirm Rejection') }}</x-danger-button>
                    </form>
                </x-modal>
            </section>
        @endcan
    </div>
</x-app-layout>
