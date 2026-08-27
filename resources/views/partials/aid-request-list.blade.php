<div class="space-y-3">
    @forelse($aidRequests as $aidRequest)
        <a href="{{ route('aid-requests.show', $aidRequest) }}" class="block bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 hover:shadow-md transition-all">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-ink-900">{{ $aidRequest->location }}</p>
                    <p class="text-[11px] text-ink-500 mt-0.5">
                        {{ __('Requested by') }} {{ $aidRequest->user->name }} · {{ $aidRequest->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    @if($aidRequest->priority === 'critical')
                        <x-status-badge status="critical" />
                    @endif
                    <x-status-badge :status="$aidRequest->status" />
                </div>
            </div>

            <div class="flex flex-wrap gap-1.5 mt-3">
                @foreach($aidRequest->requestItems as $requestItem)
                    <span class="text-[10px] font-semibold bg-ink-50 text-ink-600 border border-ink-100 rounded-full px-2.5 py-1">
                        {{ $requestItem->item->name }} × {{ number_format($requestItem->quantity) }} {{ $requestItem->item->unit }}
                    </span>
                @endforeach
            </div>
        </a>
    @empty
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-3"><x-icon name="clipboard" class="w-7 h-7" /></div>
            <p class="text-xs font-bold text-ink-500">{{ __('No aid requests yet.') }}</p>
        </div>
    @endforelse
</div>
