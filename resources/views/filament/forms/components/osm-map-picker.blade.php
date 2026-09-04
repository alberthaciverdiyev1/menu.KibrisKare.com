<div x-data="{
    lat: $wire.entangle('{{ $latStatePath ?? 'latitude' }}'),
    lng: $wire.entangle('{{ $lngStatePath ?? 'longitude' }}'),
    address: @if(!empty($addressStatePath)) $wire.entangle('{{ $addressStatePath }}') @else null @endif,
    map: null,
    marker: null,
    isUpdatingAddress: false,
    isSearchingAddress: false,
    addressSearchTimeout: null,

    init() {
        // Load Leaflet CSS and JS if not already present
        if (!document.getElementById('leaflet-css')) {
            const link = document.createElement('link');
            link.id = 'leaflet-css';
            link.rel = 'stylesheet';
            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            document.head.appendChild(link);
        }

        const setupMap = () => {
            if (this.map) return;
            const container = this.$refs.mapContainer;
            if (!container) return;

            let initialLat = parseFloat(this.lat) || 35.3403;
            let initialLng = parseFloat(this.lng) || 33.3190;

            this.map = L.map(container).setView([initialLat, initialLng], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(this.map);

            const terracottaIcon = L.divIcon({
                className: 'custom-osm-pin',
                html: `<div style='background-color: #E85D3F; width: 28px; height: 28px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;'><div style='width: 8px; height: 8px; background: white; border-radius: 50%;'></div></div>`,
                iconSize: [28, 28],
                iconAnchor: [14, 28]
            });

            this.marker = L.marker([initialLat, initialLng], {
                icon: terracottaIcon,
                draggable: true
            }).addTo(this.map);

            this.marker.on('dragend', (e) => {
                const pos = e.target.getLatLng();
                this.updateCoordinates(pos.lat, pos.lng, true);
            });

            this.map.on('click', (e) => {
                const pos = e.latlng;
                this.marker.setLatLng(pos);
                this.updateCoordinates(pos.lat, pos.lng, true);
            });

            // Re-render map after tab / layout is mounted
            setTimeout(() => {
                this.map.invalidateSize();
            }, 300);
        };

        if (window.L) {
            setupMap();
        } else {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.onload = () => setupMap();
            document.head.appendChild(script);
        }

        // Watch for manual coordinate changes
        this.$watch('lat', (val) => {
            if (this.marker && this.map && val && this.lng) {
                const pos = [parseFloat(val), parseFloat(this.lng)];
                this.marker.setLatLng(pos);
                this.map.panTo(pos);
            }
        });
        this.$watch('lng', (val) => {
            if (this.marker && this.map && val && this.lat) {
                const pos = [parseFloat(this.lat), parseFloat(val)];
                this.marker.setLatLng(pos);
                this.map.panTo(pos);
            }
        });

        // Watch for address changes (Forward Geocoding: Address -> Coordinates)
        if (this.address !== null) {
            this.$watch('address', (newVal) => {
                if (this.isUpdatingAddress) {
                    return; // Address was updated from map selection, don't re-query
                }
                if (!newVal || newVal.trim().length < 3) {
                    return;
                }

                clearTimeout(this.addressSearchTimeout);
                this.addressSearchTimeout = setTimeout(() => {
                    this.geocodeAddress(newVal.trim());
                }, 1000);
            });
        }
    },

    updateCoordinates(lat, lng, reverseGeocode = false) {
        this.lat = parseFloat(lat.toFixed(6));
        this.lng = parseFloat(lng.toFixed(6));

        if (reverseGeocode && this.address !== null) {
            this.reverseGeocodeCoordinates(this.lat, this.lng);
        }
    },

    // Map Click / Drag -> Address (Reverse Geocoding)
    reverseGeocodeCoordinates(lat, lng) {
        this.isUpdatingAddress = true;
        fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&accept-language=tr,en`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.display_name) {
                this.address = data.display_name;
            }
        })
        .catch(err => {
            console.error('OSM Reverse Geocoding Error:', err);
        })
        .finally(() => {
            setTimeout(() => {
                this.isUpdatingAddress = false;
            }, 500);
        });
    },

    // Address input -> Map (Forward Geocoding)
    geocodeAddress(query) {
        this.isSearchingAddress = true;
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1&accept-language=tr,en`, {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data && data.length > 0) {
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lon = parseFloat(result.lon);

                this.lat = parseFloat(lat.toFixed(6));
                this.lng = parseFloat(lon.toFixed(6));

                if (this.marker && this.map) {
                    const pos = [lat, lon];
                    this.marker.setLatLng(pos);
                    this.map.setView(pos, 16);
                }
            }
        })
        .catch(err => {
            console.error('OSM Geocoding Error:', err);
        })
        .finally(() => {
            this.isSearchingAddress = false;
        });
    }
}" class="w-full space-y-2">
    <div class="flex items-center justify-between text-xs text-stone-500 font-medium">
        <span class="flex items-center gap-1">
            <span>📍 Harita üzerinde tıklayarak veya pini sürükleyerek konumu seçebilirsiniz:</span>
            <span x-show="isUpdatingAddress" class="text-xs text-amber-600 font-semibold" style="display: none;">(Adres güncelleniyor...)</span>
            <span x-show="isSearchingAddress" class="text-xs text-blue-600 font-semibold" style="display: none;">(Adres aranıyor...)</span>
        </span>
        <span class="font-mono text-stone-700 dark:text-stone-300 font-bold" x-text="(lat || '0') + ', ' + (lng || '0')"></span>
    </div>
    <div x-ref="mapContainer" class="w-full h-72 rounded-xl border border-stone-300 dark:border-stone-700 overflow-hidden shadow-xs" style="min-height: 280px; z-index: 1;"></div>
</div>
