<x-app-layout>
    <div class="space-y-8">
        <x-welcome-banner :title="__('Welcome back, :name', ['name' => auth()->user()->name])" :subtitle="__('Here are the deliveries assigned to you.')" />

        <div
            class="bg-white border border-ink-100 rounded-2xl p-5 flex items-center justify-between gap-4"
            x-data="{
                sharing: localStorage.getItem('driver-location-sharing') === 'true',
                watchId: null,
                status: 'idle',
                lastSentAt: 0,
                init() {
                    if (this.sharing) this.startSharing();
                },
                toggle() {
                    this.sharing = !this.sharing;
                    localStorage.setItem('driver-location-sharing', this.sharing ? 'true' : 'false');
                    this.sharing ? this.startSharing() : this.stopSharing();
                },
                startSharing() {
                    if (!navigator.geolocation) {
                        this.status = 'error';
                        this.sharing = false;
                        return;
                    }
                    this.watchId = navigator.geolocation.watchPosition(
                        (pos) => this.sendLocation(pos.coords.latitude, pos.coords.longitude),
                        () => { this.status = 'error'; },
                        { enableHighAccuracy: true, maximumAge: 15000, timeout: 20000 }
                    );
                    this.status = 'active';
                },
                stopSharing() {
                    if (this.watchId !== null) {
                        navigator.geolocation.clearWatch(this.watchId);
                        this.watchId = null;
                    }
                    this.status = 'idle';
                    fetch('{{ route('driver.location.destroy') }}', {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' },
                    }).catch(() => {});
                },
                async sendLocation(lat, lng) {
                    const nowTs = Date.now();
                    if (this.lastSentAt && nowTs - this.lastSentAt < 15000) return;
                    this.lastSentAt = nowTs;
                    try {
                        await fetch('{{ route('driver.location.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            },
                            body: JSON.stringify({ latitude: lat, longitude: lng }),
                        });
                        this.status = 'active';
                    } catch (e) {
                        this.status = 'error';
                    }
                },
            }"
        >
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="sharing ? 'bg-field-50 text-field-600' : 'bg-ink-100 text-ink-400'">
                    <x-icon name="map" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs font-bold text-ink-900">{{ __('Share my live location') }}</p>
                    <p class="text-[11px] text-ink-500 mt-0.5" x-show="sharing && status === 'active'">{{ __('Your location is visible to depot managers and admins on the map.') }}</p>
                    <p class="text-[11px] text-ink-500 mt-0.5" x-show="!sharing">{{ __('Off — depot managers cannot see your location.') }}</p>
                    <p class="text-[11px] text-rose-600 mt-0.5" x-show="sharing && status === 'error'" x-cloak>{{ __('Could not access your location. Check your browser permissions.') }}</p>
                </div>
            </div>
            <button
                type="button"
                x-on:click="toggle()"
                role="switch"
                :aria-checked="sharing.toString()"
                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors"
                :class="sharing ? 'bg-field-600' : 'bg-ink-200'"
            >
                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform" :class="sharing ? 'translate-x-6 rtl:-translate-x-6' : 'translate-x-0.5'"></span>
            </button>
        </div>

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
