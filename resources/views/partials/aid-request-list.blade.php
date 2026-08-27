<div class="space-y-3">
    @forelse($aidRequests as $aidRequest)
        <a href="{{ route('aid-requests.show', $aidRequest) }}" class="block bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 transition-colors">
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
        <p class="text-xs text-ink-400">{{ __('No aid requests yet.') }}</p>
    @endforelse
</div>
