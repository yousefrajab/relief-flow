<x-app-layout>
    <div class="space-y-8">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-bold text-ink-900">{{ __('Field Coordination') }}</h1>
                <p class="text-xs text-ink-500 mt-1">{{ __('Submit aid requests and confirm deliveries in the field.') }}</p>
            </div>
            <a href="{{ route('aid-requests.create') }}" class="px-4 py-2.5 rounded-xl bg-field-600 hover:bg-field-700 text-white text-xs font-bold">+ {{ __('New Aid Request') }}</a>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-ink-400 uppercase tracking-wide">{{ __('My Requests') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $myRequestsCount }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <p class="text-[10px] font-bold text-amber-alert-600 uppercase tracking-wide">{{ __('Pending') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $myPendingCount }}</p>
            </div>
        </div>

        @if($myShipmentsAwaitingDelivery->isNotEmpty())
            <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4 space-y-3">
                <p class="text-xs font-bold text-sky-800">{{ __('Shipments awaiting your confirmation') }}</p>
                @foreach($myShipmentsAwaitingDelivery as $shipment)
                    <a href="{{ route('shipments.show', $shipment) }}" class="block bg-white border border-sky-100 rounded-xl p-4 hover:border-sky-300 transition-colors">
                        <p class="text-xs font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                        <p class="text-[11px] text-ink-500">{{ __('Driver') }}: {{ $shipment->driver_name }}</p>
                    </a>
                @endforeach
            </div>
        @endif

        <section class="space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-ink-900">{{ __('My Recent Requests') }}</h2>
                <a href="{{ route('aid-requests.index') }}" class="text-xs font-bold text-field-600 hover:text-field-700">{{ __('View all') }} &rarr;</a>
            </div>
            @include('partials.aid-request-list', ['aidRequests' => $recentRequests])
        </section>
    </div>
</x-app-layout>
