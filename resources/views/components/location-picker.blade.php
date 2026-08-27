@props(['latName' => 'latitude', 'lngName' => 'longitude', 'lat' => 31.5, 'lng' => 34.4667, 'zoom' => 12])

<div
    x-data="{
        lat: {{ $lat }},
        lng: {{ $lng }},
        map: null,
        marker: null,
        init() {
            this.map = L.map(this.$refs.mapEl).setView([this.lat, this.lng], {{ $zoom }});
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(this.map);
            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.lat = pos.lat;
                this.lng = pos.lng;
            });
            this.map.on('click', (e) => {
                this.lat = e.latlng.lat;
                this.lng = e.latlng.lng;
                this.marker.setLatLng(e.latlng);
            });
            setTimeout(() => this.map.invalidateSize(), 200);
        }
    }"
>
    <div x-ref="mapEl" style="height: 260px;" class="rounded-2xl border border-ink-200 z-10 relative"></div>
    <input type="hidden" name="{{ $latName }}" :value="lat">
    <input type="hidden" name="{{ $lngName }}" :value="lng">
    <p class="text-[10px] text-ink-400 mt-1.5">{{ __('Click the map or drag the pin to set the exact location.') }}</p>
</div>
