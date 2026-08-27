<x-app-layout>
    <div class="space-y-6">
        <div>
            <h1 class="text-xl font-bold text-ink-900">{{ __('Map') }}</h1>
            <p class="text-xs text-ink-500 mt-1">{{ __('Warehouses and open field requests.') }}</p>
        </div>

        <div class="flex items-center gap-4 text-[11px] font-bold">
            <span class="flex items-center gap-1.5 text-field-600"><span class="w-2.5 h-2.5 rounded-full bg-field-500"></span> {{ __('Warehouses') }}</span>
            <span class="flex items-center gap-1.5 text-amber-alert-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-alert-500"></span> {{ __('Pending Requests') }}</span>
            <span class="flex items-center gap-1.5 text-sky-600"><span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span> {{ __('Dispatched') }}</span>
        </div>

        <div id="overview-map" style="height: 480px;" class="rounded-2xl border border-ink-200"></div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var map = L.map('overview-map').setView([31.4, 34.4], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            var bounds = [];

            var warehouseIcon = L.divIcon({ className: '', html: '<div style="width:14px;height:14px;border-radius:50%;background:#1f9a79;border:2px solid white;box-shadow:0 0 0 1px #1f9a79"></div>' });
            var pendingIcon = L.divIcon({ className: '', html: '<div style="width:14px;height:14px;border-radius:50%;background:#f88a0b;border:2px solid white;box-shadow:0 0 0 1px #f88a0b"></div>' });
            var dispatchedIcon = L.divIcon({ className: '', html: '<div style="width:14px;height:14px;border-radius:50%;background:#0ea5e9;border:2px solid white;box-shadow:0 0 0 1px #0ea5e9"></div>' });

            @foreach($warehouses as $warehouse)
                L.marker([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}], { icon: warehouseIcon }).addTo(map).bindPopup(@json($warehouse->name));
                bounds.push([{{ $warehouse->latitude }}, {{ $warehouse->longitude }}]);
            @endforeach

            @foreach($aidRequests as $aidRequest)
                L.marker([{{ $aidRequest->latitude }}, {{ $aidRequest->longitude }}], { icon: {{ $aidRequest->status === 'pending' ? 'pendingIcon' : 'dispatchedIcon' }} }).addTo(map).bindPopup(@json($aidRequest->location));
                bounds.push([{{ $aidRequest->latitude }}, {{ $aidRequest->longitude }}]);
            @endforeach

            if (bounds.length > 0) {
                map.fitBounds(bounds, { padding: [40, 40] });
            }
        });
    </script>
</x-app-layout>
