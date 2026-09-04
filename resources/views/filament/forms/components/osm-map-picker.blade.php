<div x-data="{
    lat: $wire.entangle('{{ $latStatePath ?? 'latitude' }}'),
    lng: $wire.entangle('{{ $lngStatePath ?? 'longitude' }}'),
    map: null,
    marker: null,
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
                this.lat = parseFloat(pos.lat.toFixed(6));
                this.lng = parseFloat(pos.lng.toFixed(6));
            });

            this.map.on('click', (e) => {
                const pos = e.latlng;
                this.marker.setLatLng(pos);
                this.lat = parseFloat(pos.lat.toFixed(6));
                this.lng = parseFloat(pos.lng.toFixed(6));
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
    }
}" class="w-full space-y-2">
    <div class="flex items-center justify-between text-xs text-stone-500 font-medium">
        <span>📍 Harita üzerinde tıklayarak veya pini sürükleyerek konumu seçebilirsiniz:</span>
        <span class="font-mono text-stone-700 font-bold" x-text="(lat || '0') + ', ' + (lng || '0')"></span>
    </div>
    <div x-ref="mapContainer" class="w-full h-72 rounded-xl border border-stone-300 dark:border-stone-700 overflow-hidden shadow-xs" style="min-height: 280px; z-index: 1;"></div>
</div>
