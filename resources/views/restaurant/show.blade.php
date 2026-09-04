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

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center gap-3 shadow-2xs">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

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

                        <!-- Live Open Status -->
                        <div class="absolute top-4 right-4 flex items-center gap-2">
                            @if($restaurant->isOpenNow())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-white text-emerald-800 shadow-xs border border-emerald-200">
                                    <span class="w-2 h-2 rounded-full bg-open"></span>
                                    <span>Şu Anda Açık</span>
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
                                        center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}],
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
                                    L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: pin }).addTo(map);
                                }
                            });
                        }
                     }"
                     x-init="initMap()">
                </div>

                <!-- Address & Directions -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
                    <div class="space-y-1 text-xs">
                        <span class="font-bold text-ink text-sm block">{{ $restaurant->display_address }}</span>
                        <span class="text-muted block">{{ $restaurant->display_city->name ?? $restaurant->city->name }}, Kuzey Kıbrıs • Merkezden {{ $restaurant->distance }}</span>
                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}" 
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
                                <div class="p-3.5 rounded-2xl bg-sand/60 border border-warm/80 space-y-2.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-1.5 min-w-0">
                                            <span class="font-bold text-xs text-ink truncate">{{ $branch->name }}</span>
                                            @if($branch->is_main)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-terracotta/10 text-terracotta shrink-0">Merkez</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-ink bg-surface px-1.5 py-0.5 rounded border border-warm/80">
                                                <span class="text-star">★</span>
                                                <span>{{ number_format($branch->average_rating, 1) }}</span>
                                                <span class="text-stone-400 font-normal">({{ $branch->reviews_count }})</span>
                                            </span>
                                            @if($branch->city)
                                                <span class="text-[11px] font-semibold text-muted shrink-0">{{ $branch->city->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-[11px] text-muted line-clamp-1">{{ $branch->address }}</p>
                                    <div class="flex items-center justify-between pt-1 text-[11px] border-t border-warm/60">
                                        <span class="text-muted font-mono">{{ $branch->opening_hours ?? '10:00 - 23:00' }}</span>
                                        <div class="flex items-center gap-3">
                                            @if($branch->phone)
                                                <a href="tel:{{ $branch->phone }}" class="text-terracotta hover:underline font-bold">{{ $branch->phone }}</a>
                                            @endif
                                            <a href="#branch-reviews-section" class="text-ink hover:text-terracotta font-semibold text-[10px] underline">
                                                Puanla & Yorumlar
                                            </a>
                                        </div>
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
                        <span class="w-3 h-3 rounded-full {{ $restaurant->isOpenNow() ? 'bg-open' : 'bg-stone-400' }}"></span>
                    </div>

                    <!-- Current Day Banner -->
                    <div class="p-4 rounded-2xl {{ $restaurant->isOpenNow() ? 'bg-emerald-50 border border-emerald-200/60' : 'bg-sand border border-warm' }} flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold {{ $restaurant->isOpenNow() ? 'text-open' : 'text-muted' }} uppercase tracking-wider">
                                {{ $restaurant->isOpenNow() ? '● Şu Anda Servis Veriyor' : '● Şu Anda Kapalı' }}
                            </span>
                            <p class="text-base font-extrabold text-ink mt-0.5">
                                {{ $restaurant->getTodayHours() }}
                            </p>
                        </div>
                        <span class="text-xs font-bold text-muted bg-white px-2.5 py-1 rounded-lg border border-warm">
                            Bugün
                        </span>
                    </div>

                    @php
                        $scheduleSource = $restaurant->branches->where('is_main', true)->first() ?? $restaurant->branches->first() ?? $restaurant;
                        $weeklyHours = $scheduleSource->weekly_hours ?? $restaurant->weekly_hours;
                    @endphp

                    @if(!empty($weeklyHours) && is_array($weeklyHours))
                        <!-- 7 Günlük Açılış - Kapanış Tablosu -->
                        <div class="pt-3 border-t border-warm space-y-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-muted block pb-1">Haftalık Saatler</span>
                            @php
                                $daysMap = [
                                    'monday' => 'Pazartesi',
                                    'tuesday' => 'Salı',
                                    'wednesday' => 'Çarşamba',
                                    'thursday' => 'Perşembe',
                                    'friday' => 'Cuma',
                                    'saturday' => 'Cumartesi',
                                    'sunday' => 'Pazar',
                                ];
                                $currentDayKey = strtolower(now()->format('l'));
                            @endphp

                            <div class="space-y-1.5 text-xs">
                                @foreach($daysMap as $dayKey => $dayName)
                                    @php
                                        $dayConfig = $weeklyHours[$dayKey] ?? null;
                                        $isToday = ($dayKey === $currentDayKey);
                                    @endphp
                                    <div class="flex items-center justify-between py-1 px-2.5 rounded-lg {{ $isToday ? 'bg-sand font-bold text-ink border border-warm' : 'text-muted' }}">
                                        <div class="flex items-center gap-1.5">
                                            <span>{{ $dayName }}</span>
                                            @if($isToday)
                                                <span class="text-[10px] bg-terracotta text-white px-1.5 py-0.2 rounded font-extrabold">Bugün</span>
                                            @endif
                                        </div>
                                        <div>
                                            @if(!empty($dayConfig['is_closed']))
                                                <span class="text-stone-400 font-semibold">Kapalı</span>
                                            @elseif(!empty($dayConfig['open']) && !empty($dayConfig['close']))
                                                <span class="font-mono text-ink">{{ $dayConfig['open'] }} - {{ $dayConfig['close'] }}</span>
                                            @else
                                                <span class="font-mono text-ink">{{ $scheduleSource->opening_hours ?? '10:00 - 23:00' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

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

        <!-- 3.5 BRANCH REVIEWS & ANONYMOUS RATING SECTION -->
        <section id="branch-reviews-section" 
                 class="bg-surface rounded-3xl border border-warm p-6 sm:p-10 shadow-xs space-y-8"
                 x-data="{
                    activeBranchId: '{{ $restaurant->branches->first()->id ?? 0 }}',
                    selectedRating: 5,
                    showForm: false
                 }">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-warm">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block">Misafir Deneyimleri</span>
                    <h2 class="text-2xl font-black text-ink mt-0.5 flex items-center gap-2">
                        <span>⭐ Şube Puanları ve Yorumlar</span>
                    </h2>
                    <p class="text-xs text-muted mt-1">Ziyaret ettiğiniz şubeyi seçip anonim olarak yıldız ve yorum bırakabilirsiniz.</p>
                </div>

                <button type="button"
                        @click="showForm = !showForm"
                        class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-xs transition-colors shrink-0">
                    <span x-text="showForm ? 'Vazgeç' : '+ Yorum & Puan Ekle'"></span>
                </button>
            </div>

            <!-- Branch Selector Tabs -->
            @if($restaurant->branches->count() > 1)
                <div class="flex items-center gap-2 overflow-x-auto pb-2 hide-scrollbar">
                    @foreach($restaurant->branches as $b)
                        <button type="button"
                                @click="activeBranchId = '{{ $b->id }}'"
                                :class="activeBranchId == '{{ $b->id }}' ? 'bg-ink text-white font-bold' : 'bg-sand text-ink hover:bg-sand/80 font-semibold border border-warm'"
                                class="px-4 py-2.5 rounded-xl text-xs shrink-0 flex items-center gap-2 transition-colors cursor-pointer">
                            <span>{{ $b->name }}</span>
                            <span class="text-[11px] px-1.5 py-0.2 rounded font-bold" :class="activeBranchId == '{{ $b->id }}' ? 'bg-white/20 text-white' : 'bg-surface text-ink border border-warm'">
                                ★ {{ number_format($b->average_rating, 1) }}
                            </span>
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Review Submission Form (Accordion / Dropdown) -->
            <div x-show="showForm" x-collapse class="p-6 rounded-2xl bg-sand/60 border border-warm space-y-4">
                <h3 class="text-sm font-extrabold text-ink">Şubeyi Değerlendir (Anonim)</h3>
                
                @foreach($restaurant->branches as $b)
                    <form x-show="activeBranchId == '{{ $b->id }}'" 
                          action="{{ route('branches.reviews.store', $b->id) }}" 
                          method="POST" 
                          class="space-y-4">
                        @csrf
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Star Selection -->
                            <div>
                                <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Puanınız (1 - 5 Yıldız)</label>
                                <div class="flex items-center gap-2">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button" 
                                                @click="selectedRating = star"
                                                class="text-2xl transition-transform hover:scale-110 focus:outline-none">
                                            <span :class="star <= selectedRating ? 'text-amber-500' : 'text-stone-300'">★</span>
                                        </button>
                                    </template>
                                    <span class="text-xs font-bold text-ink font-mono ml-2" x-text="selectedRating + ' / 5 Yıldız'"></span>
                                </div>
                                <input type="hidden" name="rating" :value="selectedRating">
                            </div>

                            <!-- Author Name (Optional) -->
                            <div>
                                <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">İsim veya Rumuz (İsteğe Bağlı)</label>
                                <input type="text" 
                                       name="author_name" 
                                       placeholder="Örn: Mehmet K. veya boş bırakın (Anonim)" 
                                       class="w-full px-4 py-2.5 bg-surface border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium">
                            </div>
                        </div>

                        <!-- Comment Textarea -->
                        <div>
                            <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Yorumunuz & Deneyiminiz</label>
                            <textarea name="comment" 
                                      rows="3" 
                                      placeholder="{{ $b->name }} şubesindeki lezzet, servis, ambiyans hakkında düşünceleriniz..." 
                                      class="w-full px-4 py-3 bg-surface border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-2">
                            <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-xs font-bold text-muted hover:text-ink">
                                İptal
                            </button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-xs">
                                Yorumu Gönder
                            </button>
                        </div>
                    </form>
                @endforeach
            </div>

            <!-- Branch Reviews Display List -->
            @foreach($restaurant->branches as $b)
                <div x-show="activeBranchId == '{{ $b->id }}'" class="space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-warm/60">
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold text-sm text-ink">{{ $b->name }}</span>
                            <span class="text-xs text-muted">({{ $b->reviews_count }} yorum)</span>
                        </div>
                        <div class="flex items-center gap-1.5 text-xs font-bold text-ink">
                            <span class="text-star text-sm">★</span>
                            <span>{{ number_format($b->average_rating, 1) }} Ort. Puan</span>
                        </div>
                    </div>

                    @if($b->reviews->isEmpty())
                        <div class="p-8 text-center bg-sand/40 rounded-2xl border border-dashed border-warm text-muted text-xs space-y-2">
                            <span class="text-2xl block">💬</span>
                            <p class="font-semibold text-ink">Bu şube için henüz yorum yapılmamış.</p>
                            <p>İlk puan veren ve deneyimini paylaşan siz olun!</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($b->reviews as $rev)
                                <div class="p-4 rounded-2xl bg-sand/40 border border-warm/80 space-y-2.5">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-terracotta/10 text-terracotta font-bold text-xs flex items-center justify-center">
                                                {{ mb_substr($rev->author_name, 0, 1) }}
                                            </div>
                                            <span class="text-xs font-bold text-ink">{{ $rev->author_name }}</span>
                                        </div>
                                        <div class="flex items-center gap-1 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $rev->rating ? 'text-amber-500' : 'text-stone-300' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rev->comment)
                                        <p class="text-xs text-ink/90 leading-relaxed">{{ $rev->comment }}</p>
                                    @endif
                                    <div class="text-[10px] text-muted font-mono pt-1">
                                        {{ $rev->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach

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
