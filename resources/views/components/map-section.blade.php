@props(['restaurants', 'selectedCity'])

<div class="relative bg-surface rounded-2xl border border-warm overflow-hidden shadow-xs" 
     x-data="cyprusMapComponent({
        centerLat: {{ $selectedCity->latitude ?? 35.3403 }},
        centerLng: {{ $selectedCity->longitude ?? 33.3190 }},
        zoom: {{ ($selectedCity->slug ?? '') == 'all' ? 9 : 13 }},
        restaurants: @json($restaurants ?? [])
     })"
     x-init="initMap()">
    
    <!-- Map Header Toolbar -->
    <div class="p-4 sm:p-5 border-b border-warm flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-sand">
        <div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-terracotta"></span>
                <h3 class="font-bold text-base text-ink">
                    {{ $selectedCity->name ?? 'Kıbrıs' }} Haritası
                </h3>
            </div>
            <p class="text-xs text-muted mt-0.5 font-medium">Mekan pinlerine tıklayarak fotoğraf ve menü detaylarını görüntüleyin</p>
        </div>

        <div class="flex items-center gap-2">
            <button @click="resetView()" type="button" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-warm text-xs font-bold text-ink hover:border-muted shadow-2xs">
                <svg class="w-3.5 h-3.5 text-terracotta" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                <span>Merkeze Odakla</span>
            </button>
            <span class="text-xs font-bold text-muted bg-surface px-3 py-1.5 rounded-lg border border-warm">
                <span x-text="restaurants.length" class="text-ink"></span> Mekan
            </span>
        </div>
    </div>

    <!-- Map container -->
    <div id="cyprus-leaflet-map" class="w-full h-[460px] sm:h-[520px] z-10"></div>
</div>

<script>
function cyprusMapComponent(config) {
    return {
        map: null,
        markers: [],
        restaurants: config.restaurants || [],
        centerLat: config.centerLat,
        centerLng: config.centerLng,
        zoom: config.zoom,

        initMap() {
            this.$nextTick(() => {
                if (typeof L === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = () => this.setupLeaflet();
                    document.head.appendChild(script);
                } else {
                    this.setupLeaflet();
                }
            });
        },

        setupLeaflet() {
            const container = document.getElementById('cyprus-leaflet-map');
            if (!container || this.map) return;

            this.map = L.map('cyprus-leaflet-map', {
                center: [this.centerLat, this.centerLng],
                zoom: this.zoom,
                scrollWheelZoom: false
            });

            // Clean Voyager tiles
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                maxZoom: 19
            }).addTo(this.map);

            // Refined Terracotta Map Pin
            const customIcon = L.divIcon({
                className: 'custom-terracotta-marker',
                html: `<div style="background: #E85D3F; color: white; padding: 4px 9px; border-radius: 9999px; font-weight: 800; font-size: 11px; font-family: sans-serif; display: flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(232, 93, 63, 0.4); border: 2px solid white; white-space: nowrap; cursor: pointer;">
                        <span style="color: #FFD278;">★</span>
                        <span>Mekan</span>
                       </div>`,
                iconSize: [64, 26],
                iconAnchor: [32, 13],
                popupAnchor: [0, -14]
            });

            // Add pins
            this.restaurants.forEach(rest => {
                if (!rest.lat || !rest.lng) return;

                const marker = L.marker([rest.lat, rest.lng], { icon: customIcon }).addTo(this.map);

                // Refined popup card matching Mediterranean palette
                const popupContent = `
                    <div style="font-family: 'Plus Jakarta Sans', sans-serif; width: 230px; padding: 2px;">
                        <img src="${rest.image}" alt="${rest.name}" style="width: 100%; height: 115px; object-fit: cover; border-radius: 8px; margin-bottom: 8px;">
                        <div style="font-size: 10px; font-weight: 800; text-transform: uppercase; color: #737373; letter-spacing: 0.05em; margin-bottom: 2px;">${rest.city} • ${rest.cuisine}</div>
                        <h4 style="font-weight: 800; font-size: 14px; margin: 0 0 4px 0; color: #191919; line-height: 1.2;">${rest.name}</h4>
                        <div style="display: flex; align-items: center; gap: 6px; font-size: 11px; margin-bottom: 10px; color: #737373;">
                            <span style="color: #D99A24; font-weight: 800;">★ ${rest.rating}</span>
                            <span>•</span>
                            <span>${rest.distance}</span>
                            <span>•</span>
                            <span style="font-weight: 800; color: #191919;">${rest.price_range}</span>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <a href="${rest.detail_url}" 
                               style="flex: 1; text-align: center; text-decoration: none; background: #FAF8F4; color: #191919; padding: 6px 8px; border-radius: 8px; font-weight: 700; font-size: 11px; border: 1px solid #E8E3DC;">
                                Detay
                            </a>
                            <a href="${rest.menu_url}" 
                               style="flex: 1.3; text-align: center; text-decoration: none; background: #E85D3F; color: white; padding: 6px 8px; border-radius: 8px; font-weight: 800; font-size: 11px;">
                                Menüyü Gör →
                            </a>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                this.markers.push(marker);
            });
        },

        resetView() {
            if (this.map) {
                this.map.setView([this.centerLat, this.centerLng], this.zoom);
            }
        }
    };
}
</script>
