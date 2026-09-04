@extends('layouts.app')

@section('title', $restaurant->name . " — Kıbrıs Restoran Rehberi & Menü | AdaMenü")

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-10">

        <!-- TOP BAR: RETURN & QUICK TAGS -->
        <div class="flex items-center justify-between">
            <a href="{{ route('restaurants.index') }}" 
               class="inline-flex items-center gap-2 text-xs font-bold text-muted hover:text-ink">
                <svg class="w-4 h-4 text-ink" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                <span>Tüm Restoranları Keşfet</span>
            </a>

            <div class="flex items-center gap-2 text-xs">
                <span class="font-bold text-ink">{{ $restaurant->city->name }}</span>
                <span class="text-muted">•</span>
                <span class="text-muted">{{ $restaurant->cuisine }}</span>
            </div>
        </div>

        <!-- 1. EDITORIAL RESTAURANT SHOWCASE (Hero Profile) -->
        <section class="bg-surface rounded-3xl border border-warm p-6 sm:p-10 shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                
                <!-- PHOTO HERO WITH LIVE STATUS & BADGES -->
                <div class="lg:col-span-7 w-full">
                    <div class="aspect-[16/10] sm:aspect-[16/10] w-full rounded-2xl overflow-hidden bg-sand border border-warm relative shadow-xs">
                        <img src="{{ $restaurant->image }}" 
                             alt="{{ $restaurant->name }}" 
                             class="w-full h-full object-cover">

                        <!-- Rating Badge (Top Left) -->
                        <div class="absolute top-4 left-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-black bg-ink text-white shadow-xs">
                                <span class="text-star font-bold text-sm">★</span>
                                <span class="text-sm font-extrabold">{{ number_format($restaurant->rating, 1) }}</span>
                                <span class="text-stone-300 font-normal text-[11px]">({{ $restaurant->reviews_count }} yorum)</span>
                            </span>
                        </div>

                        <!-- Open/Close Status (Top Right) -->
                        <div class="absolute top-4 right-4">
                            @if($restaurant->is_open)
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold bg-open text-white shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                    Şu Anda Açık
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-ink text-stone-300 shadow-xs border border-stone-700">
                                    Şu Anda Kapalı
                                </span>
                            @endif
                        </div>

                        <!-- Bottom Location Strip on Photo -->
                        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-xs font-bold">
                            <span class="px-3 py-1.5 rounded-lg bg-ink/90 text-white shadow-xs backdrop-blur-xs">
                                📍 {{ $restaurant->city->name }} • {{ $restaurant->distance }}
                            </span>
                            <span class="px-3 py-1.5 rounded-lg bg-surface text-ink shadow-xs border border-warm font-mono">
                                {{ $restaurant->price_range }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- RESTAURANT THESIS & KEY ACTIONS -->
                <div class="lg:col-span-5 flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <!-- Eyebrow categories -->
                        <div class="flex items-center gap-2 text-xs flex-wrap">
                            <span class="px-3 py-1 rounded-full font-bold bg-sand text-terracotta border border-warm">
                                {{ $restaurant->cuisine }}
                            </span>
                            <span class="px-3 py-1 rounded-full font-bold bg-sand text-ink border border-warm">
                                {{ $restaurant->city->name }}, Kuzey Kıbrıs
                            </span>
                        </div>

                        <!-- Headline -->
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight leading-tight">
                            {{ $restaurant->name }}
                        </h1>

                        <!-- Editorial Description -->
                        <p class="text-sm sm:text-base text-muted leading-relaxed font-normal">
                            {{ $restaurant->description }}
                        </p>

                        <!-- Quick facts line -->
                        <div class="pt-3 border-t border-warm grid grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-muted block text-[11px] font-bold uppercase tracking-wider">Çalışma Saatleri</span>
                                <span class="text-ink font-extrabold mt-0.5 block">Her Gün: {{ $restaurant->opening_hours }}</span>
                            </div>
                            <div>
                                <span class="text-muted block text-[11px] font-bold uppercase tracking-wider">Menü Çeşidi</span>
                                <span class="text-ink font-extrabold mt-0.5 block">{{ $restaurant->menuItems->count() }} Seçenek Listelendi</span>
                            </div>
                        </div>
                    </div>

                    <!-- PRIMARY SIGNATURE ACTIONS -->
                    <div class="pt-6 border-t border-warm space-y-3">
                        <!-- Main Digital Menu CTA -->
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                           class="w-full inline-flex items-center justify-center gap-3 px-6 py-4 rounded-2xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-xs text-center">
                            <span>📖 Dijital Menüyü ve Fiyatları Gör</span>
                            <span class="text-xs bg-black/15 px-2 py-0.5 rounded-md font-mono">({{ $restaurant->menuItems->count() }} Çeşit)</span>
                        </a>

                        <!-- Secondary Actions Row -->
                        <div class="grid grid-cols-2 gap-3">
                            @if($restaurant->phone)
                                <a href="tel:{{ $restaurant->phone }}" 
                                   class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs text-center">
                                    <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                                    <span>{{ $restaurant->phone }}</span>
                                </a>
                            @else
                                <div class="inline-flex items-center justify-center px-4 py-3 rounded-xl bg-sand text-muted text-xs font-semibold">
                                    Telefon Belirtilmedi
                                </div>
                            @endif

                            <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->latitude }},{{ $restaurant->longitude }}" 
                               target="_blank"
                               rel="noopener"
                               class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs text-center">
                                <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
                                <span>Yol Tarifi Al</span>
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </section>

        <!-- 2. SIGNATURE DISHES TASTING PREVIEW -->
        @if(isset($featuredItems) && $featuredItems->isNotEmpty())
            <section class="bg-surface rounded-3xl border border-warm p-6 sm:p-10 shadow-xs space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 pb-5 border-b border-warm">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-terracotta block">Şefin Tavsiyesi & Popüler Seçimler</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-ink mt-1">
                            Öne Çıkan Lezzetler
                        </h2>
                        <p class="text-xs sm:text-sm text-muted mt-1 font-medium">
                            Bu mekanda misafirlerin en çok sipariş ettiği imza yemekler ve porsiyon fiyatları
                        </p>
                    </div>

                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-terracotta hover:text-terracotta-dark uppercase tracking-wider shrink-0">
                        <span>Tüm Menüyü İncele ({{ $restaurant->menuItems->count() }} Çeşit)</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- 4-Col / 2-Col Dish Showcase -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    @foreach($featuredItems as $dish)
                        <x-menu-item-card :dish="$dish" :showMenuLink="true" :slug="$restaurant->slug" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. PRACTICAL LOGISTICS: LOCATION, HOURS & AMENITIES -->
        <section class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT 7 COLS: Interactive Location & Address -->
            <div class="lg:col-span-7 bg-surface rounded-3xl border border-warm p-6 sm:p-8 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-warm">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-muted block">Konum & Ulaşım</span>
                        <h3 class="text-xl font-black text-ink mt-0.5">Mekanın Adresi ve Haritası</h3>
                    </div>
                    <span class="text-xs font-bold px-3 py-1 rounded-lg bg-sand text-ink border border-warm">
                        📍 {{ $restaurant->city->name }}
                    </span>
                </div>

                <!-- Interactive Leaflet Mini-Map -->
                <div class="w-full h-64 sm:h-72 rounded-2xl overflow-hidden border border-warm relative"
                     x-data="{
                        initMap() {
                            this.$nextTick(() => {
                                if (typeof L !== 'undefined') {
                                    const map = L.map($el, {
                                        center: [{{ $restaurant->latitude }}, {{ $restaurant->longitude }}],
                                        zoom: 15,
                                        scrollWheelZoom: false,
                                        zoomControl: false
                                    });
                                    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                        maxZoom: 19
                                    }).addTo(map);
                                    
                                    const pin = L.divIcon({
                                        className: 'custom-pin',
                                        html: `<div style='background:#E85D3F;color:white;padding:5px 10px;border-radius:9999px;font-weight:bold;font-size:11px;box-shadow:0 4px 12px rgba(232,93,63,0.4);border:2px solid white;'>★ {{ addslashes($restaurant->name) }}</div>`,
                                        iconSize: [80, 26],
                                        iconAnchor: [40, 13]
                                    });
                                    L.marker([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], { icon: pin }).addTo(map);
                                }
                            });
                        }
                     }"
                     x-init="initMap()">
                </div>

                <!-- Address & Directions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                    <div class="space-y-1 text-xs">
                        <span class="font-bold text-ink text-sm block">{{ $restaurant->address }}</span>
                        <span class="text-muted block">{{ $restaurant->city->name }}, Kuzey Kıbrıs • Merkezden {{ $restaurant->distance }}</span>
                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->latitude }},{{ $restaurant->longitude }}" 
                       target="_blank"
                       rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-xs uppercase tracking-wider shrink-0 shadow-xs">
                        <span>Google Haritalarda Aç ↗</span>
                    </a>
                </div>

                @if($restaurant->branches && $restaurant->branches->count() > 1)
                    <!-- Branches List (Tüm Şubeler) -->
                    <div class="pt-5 border-t border-warm space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted block">Tüm Şubeler ({{ $restaurant->branches->count() }})</span>
                            <span class="text-xs font-medium text-terracotta">Aynı lezzet, farklı lokasyonlar</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($restaurant->branches as $branch)
                                <div class="p-3.5 rounded-2xl bg-sand/60 border border-warm/80 space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="font-bold text-xs text-ink truncate">{{ $branch->name }}</span>
                                            @if($branch->is_main)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-terracotta/10 text-terracotta shrink-0">Merkez</span>
                                            @endif
                                        </div>
                                        @if($branch->city)
                                            <span class="text-[11px] font-semibold text-muted shrink-0">{{ $branch->city->name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-muted line-clamp-1">{{ $branch->address }}</p>
                                    <div class="flex items-center justify-between pt-1 text-[11px] border-t border-warm/60">
                                        <span class="text-muted font-mono">{{ $branch->opening_hours ?? '10:00 - 23:00' }}</span>
                                        @if($branch->phone)
                                            <a href="tel:{{ $branch->phone }}" class="text-terracotta hover:underline font-bold">{{ $branch->phone }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- RIGHT 5 COLS: Operating Hours & Verified Amenities -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Hours Card -->
                <div class="bg-surface rounded-3xl border border-warm p-6 sm:p-8 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-warm">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-muted block">Ziyaret Saatleri</span>
                            <h3 class="text-xl font-black text-ink mt-0.5">Çalışma Düzeni</h3>
                        </div>
                        <span class="w-3 h-3 rounded-full {{ $restaurant->is_open ? 'bg-open' : 'bg-stone-400' }}"></span>
                    </div>

                    <!-- Current Day Banner -->
                    <div class="p-4 rounded-2xl {{ $restaurant->is_open ? 'bg-emerald-50 border border-emerald-200/60' : 'bg-sand border border-warm' }} flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold {{ $restaurant->is_open ? 'text-open' : 'text-muted' }} uppercase tracking-wider">
                                {{ $restaurant->is_open ? '● Şu Anda Servis Veriyor' : '● Kapalı' }}
                            </span>
                            <p class="text-base font-extrabold text-ink mt-0.5">
                                {{ $restaurant->opening_hours }}
                            </p>
                        </div>
                        <span class="text-xs font-bold text-muted bg-white px-2.5 py-1 rounded-lg border border-warm">
                            Hergün
                        </span>
                    </div>

                    <p class="text-xs text-muted leading-relaxed">
                        Mutfak sipariş alımı kapanış saatinden 45 dakika önce sona ermektedir. Yoğun saatler için rezervasyon önerilir.
                    </p>
                </div>

                <!-- Verified Amenities Chips -->
                <div class="bg-surface rounded-3xl border border-warm p-6 sm:p-8 shadow-xs space-y-4">
                    <h3 class="text-sm font-extrabold text-ink uppercase tracking-wider pb-3 border-b border-warm">
                        Mekan Özellikleri & İmkanları
                    </h3>

                    <div class="grid grid-cols-2 gap-2.5 text-xs font-bold text-ink">
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Açık Teras & Bahçe</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Ücretsiz Hızlı Wi-Fi</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Otopark Alanı</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Kredi Kartı Geçerli</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Paket Servis / Gel-Al</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-sand border border-warm">
                            <span class="text-open font-black">✓</span>
                            <span>Masa Rezervasyonu</span>
                        </div>
                    </div>
                </div>

            </div>

        </section>

        <!-- 4. RELATED RESTAURANTS (Neighborhood Alternatives) -->
        @if($relatedRestaurants->isNotEmpty())
            <section class="pt-8 border-t border-warm space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-muted block">Bölge Keşfi</span>
                        <h2 class="text-2xl font-black text-ink mt-0.5">
                            {{ $restaurant->city->name }} Bölgesindeki Diğer Restoranlar
                        </h2>
                        <p class="text-xs text-muted mt-1">Aynı şehirdeki doğrulanmış alternatif mekanlar</p>
                    </div>

                    <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" 
                       class="text-xs font-bold text-terracotta hover:text-terracotta-dark uppercase tracking-wider shrink-0">
                        {{ $restaurant->city->name }} Tüm Mekanlar →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedRestaurants as $rel)
                        <x-restaurant-card :restaurant="$rel" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>

@endsection
