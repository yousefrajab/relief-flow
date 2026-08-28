<x-app-layout>
    <div class="space-y-8">
        <x-welcome-banner :title="__('Welcome back, :name', ['name' => auth()->user()->name])" :subtitle="__('Here are the deliveries assigned to you.')" />

        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <div class="w-9 h-9 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center mb-3"><x-icon name="truck" class="w-4.5 h-4.5" /></div>
                <p class="text-[10px] font-bold text-sky-600 uppercase tracking-wide">{{ __('Active Deliveries') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $activeDeliveries->count() }}</p>
            </div>
            <div class="bg-white border border-ink-100 rounded-2xl p-5">
                <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center mb-3"><x-icon name="check-circle" class="w-4.5 h-4.5" /></div>
                <p class="text-[10px] font-bold text-field-600 uppercase tracking-wide">{{ __('Delivered') }}</p>
                <p class="text-2xl font-extrabold text-ink-900 mt-1">{{ $deliveredCount }}</p>
            </div>
        </div>

        <section class="space-y-4">
            <h2 class="text-sm font-bold text-ink-900">{{ __('Deliveries awaiting you') }}</h2>
            <div class="space-y-3">
                @forelse($activeDeliveries as $shipment)
                    <a href="{{ route('shipments.show', $shipment) }}" class="block bg-white border border-ink-100 rounded-2xl p-5 hover:border-field-300 hover:shadow-md transition-all">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0"><x-icon name="truck" class="w-5 h-5" /></div>
                                <div>
                                    <p class="text-sm font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                                    <p class="text-[11px] text-ink-500 mt-0.5">{{ __('Pick up from') }} {{ $shipment->warehouse->name }}</p>
                                    <p class="text-[11px] text-ink-400 mt-0.5">{{ $shipment->qr_code_token }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$shipment->status" />
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-16 text-center bg-white border border-ink-100 rounded-2xl">
                        <div class="w-14 h-14 rounded-2xl bg-ink-100 text-ink-400 flex items-center justify-center mb-3"><x-icon name="truck" class="w-7 h-7" /></div>
                        <p class="text-xs font-bold text-ink-500">{{ __('No deliveries assigned to you right now.') }}</p>
                    </div>
                @endforelse
            </div>
        </section>

        @if($recentDeliveries->isNotEmpty())
            <section class="space-y-4">
                <h2 class="text-sm font-bold text-ink-900">{{ __('Recently delivered') }}</h2>
                <div class="space-y-2">
                    @foreach($recentDeliveries as $shipment)
                        <a href="{{ route('shipments.show', $shipment) }}" class="flex items-center justify-between gap-3 bg-white border border-ink-100 rounded-2xl p-4 hover:border-field-300 hover:shadow-md transition-all">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-field-50 text-field-600 flex items-center justify-center shrink-0"><x-icon name="check-circle" class="w-4.5 h-4.5" /></div>
                                <div>
                                    <p class="text-xs font-bold text-ink-900">{{ $shipment->aidRequest->location }}</p>
                                    <p class="text-[11px] text-ink-500">{{ $shipment->delivered_at?->diffForHumans() }}</p>
                                </div>
                            </div>
                            <x-status-badge :status="$shipment->status" />
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</x-app-layout>
