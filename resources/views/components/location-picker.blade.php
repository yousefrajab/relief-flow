@props(['latName' => 'latitude', 'lngName' => 'longitude', 'lat' => 31.5, 'lng' => 34.4667, 'zoom' => 12])

<div
    x-data="{
        lat: {{ $lat }},
        lng: {{ $lng }},
        map: null,
        marker: null,
        geocoding: false,
        init() {
            this.map = L.map(this.$refs.mapEl).setView([this.lat, this.lng], {{ $zoom }});
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(this.map);
            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
            this.marker.on('dragend', () => {
                const pos = this.marker.getLatLng();
                this.setPosition(pos.lat, pos.lng);
            });
            this.map.on('click', (e) => {
                this.marker.setLatLng(e.latlng);
                this.setPosition(e.latlng.lat, e.latlng.lng);
            });
            new ResizeObserver(() => this.map.invalidateSize()).observe(this.$refs.mapEl);
        },
        setPosition(lat, lng) {
            this.lat = lat;
            this.lng = lng;
            this.reverseGeocode(lat, lng);
        },
        async reverseGeocode(lat, lng) {
            this.geocoding = true;
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language={{ app()->getLocale() }}`);
                const data = await response.json();
                if (data.display_name) {
                    this.$dispatch('location-picked', data.display_name);
                }
            } catch (e) {
                // Geocoding is a convenience only — the lat/lng fields below remain authoritative either way.
            } finally {
                this.geocoding = false;
            }
        }
    }"
>
    <div x-ref="mapEl" style="height: 300px;" class="rounded-2xl border border-ink-200 z-10 relative"></div>
    <input type="hidden" name="{{ $latName }}" :value="lat">
    <input type="hidden" name="{{ $lngName }}" :value="lng">
    <p class="text-[10px] text-ink-400 mt-1.5" x-show="!geocoding">{{ __('Click the map or drag the pin to set the exact location.') }}</p>
    <p class="text-[10px] text-field-600 font-semibold mt-1.5" x-show="geocoding" x-cloak>{{ __('Looking up address…') }}</p>
</div>
