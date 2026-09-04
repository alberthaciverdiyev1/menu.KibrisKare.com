<!-- ================= KONUM & HARİTA ENTEGRASYONU ================= -->
<section id="konum" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                <x-ico name="map-pin" class="w-5 h-5 text-terracotta" />
                <span>Konum & Harita</span>
            </h2>
            @if($address)
                <p class="text-xs text-muted mt-1">{{ $address }}</p>
            @endif
        </div>
        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-2xs transition-colors">
            <x-ico name="navigation" class="w-3.5 h-3.5" />
            <span>Google Haritalar'da Yol Tarifi</span>
        </a>
    </div>

    @php
        $branchMarkers = $restaurant->branches->map(function($b) use ($restaurant) {
            return [
                'name' => $b->name,
                'address' => $b->address,
                'city' => $b->city->name ?? '',
                'phone' => $b->phone,
                'lat' => (float) $b->latitude,
                'lng' => (float) $b->longitude,
                'is_main' => (bool) $b->is_main,
                'is_open' => $b->isOpenNow(),
                'menu_url' => route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $b->id]),
            ];
        })->filter(fn($b) => !empty($b['lat']) && !empty($b['lng']))->values();
    @endphp

    <!-- Leaflet Interactive Map Container -->
    <div id="restaurant-detail-map" class="h-64 sm:h-80 w-full rounded-xl overflow-hidden relative shadow-inner border border-stone-200/60 bg-stone-100 z-10"></div>

    <script>
        (function() {
            function initRestaurantDetailMap() {
                if (typeof L === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                    script.onload = renderRestaurantDetailMap;
                    document.head.appendChild(script);
                } else {
                    renderRestaurantDetailMap();
                }
            }

            function renderRestaurantDetailMap() {
                const container = document.getElementById('restaurant-detail-map');
                if (!container || container._leaflet_id) return;

                const defaultLat = {{ (float) ($restaurant->display_latitude ?? 35.3403) }};
                const defaultLng = {{ (float) ($restaurant->display_longitude ?? 33.3190) }};
                const branches = @json($branchMarkers);

                const map = L.map(container, {
                    center: [defaultLat, defaultLng],
                    zoom: branches.length > 1 ? 12 : 15,
                    scrollWheelZoom: false
                });

                L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                    attribution: '&copy; OpenStreetMap &copy; CARTO',
                    maxZoom: 19
                }).addTo(map);

                if (branches.length > 0) {
                    const bounds = [];
                    branches.forEach(b => {
                        const customPin = L.divIcon({
                            className: 'custom-restaurant-pin',
                            html: '<div style="background: ' + (b.is_main ? '#E85D3F' : '#191919') + '; color: white; padding: 4px 9px; border-radius: 9999px; font-weight: 800; font-size: 11px; font-family: sans-serif; display: flex; align-items: center; gap: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 2px solid white; white-space: nowrap; cursor: pointer;">' +
                                    '<span style="color: #FFD278;">★</span>' +
                                    '<span>' + b.name + '</span>' +
                                  '</div>',
                            iconSize: [110, 26],
                            iconAnchor: [55, 13]
                        });

                        const marker = L.marker([b.lat, b.lng], { icon: customPin }).addTo(map);
                        marker.bindPopup(
                            '<div style="font-family: sans-serif; min-width: 160px; padding: 2px;">' +
                                '<b style="font-size: 13px; color: #191919;">' + b.name + '</b>' +
                                (b.address ? '<p style="font-size: 11px; color: #666; margin: 3px 0 6px;">' + b.address + '</p>' : '') +
                                '<a href="' + b.menu_url + '" style="display: inline-block; background: #E85D3F; color: white; text-decoration: none; font-size: 11px; font-weight: bold; padding: 4px 8px; border-radius: 6px;">Menüyü Aç →</a>' +
                            '</div>'
                        );
                        bounds.push([b.lat, b.lng]);
                    });

                    if (bounds.length > 1) {
                        map.fitBounds(bounds, { padding: [30, 30] });
                    } else if (bounds.length === 1) {
                        map.setView(bounds[0], 15);
                    }
                } else {
                    const customPin = L.divIcon({
                        className: 'custom-restaurant-pin',
                        html: '<div style="background: #E85D3F; color: white; padding: 5px 11px; border-radius: 9999px; font-weight: 800; font-size: 11px; font-family: sans-serif; display: flex; align-items: center; gap: 5px; box-shadow: 0 4px 12px rgba(232, 93, 63, 0.4); border: 2px solid white; white-space: nowrap;">' +
                                '<span style="color: #FFD278;">★</span>' +
                                '<span>{{ addslashes($restaurant->name) }}</span>' +
                              '</div>',
                        iconSize: [120, 28],
                        iconAnchor: [60, 14]
                    });

                    L.marker([defaultLat, defaultLng], { icon: customPin }).addTo(map)
                     .bindPopup('<b>{{ addslashes($restaurant->name) }}</b><br><small>{{ addslashes($address) }}</small>')
                     .openPopup();
                }
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initRestaurantDetailMap);
            } else {
                initRestaurantDetailMap();
            }
        })();
    </script>
</section>
