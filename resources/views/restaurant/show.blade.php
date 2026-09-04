@extends('layouts.app')

@section('title', $restaurant->name . " — Mekan Detayı ve Menü | AdaMenü Kıbrıs")

@section('content')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8 space-y-8">

        <!-- TOP NAVIGATION & RETURN -->
        <div class="flex items-center justify-between">
            <a href="{{ route('restaurants.index') }}" 
               class="inline-flex items-center gap-1.5 text-xs font-bold text-muted hover:text-ink">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                <span>Tüm Restoranlara Dön</span>
            </a>

            <div class="flex items-center gap-2 text-xs font-semibold text-muted">
                <span>{{ $restaurant->city->name }}</span>
                <span>•</span>
                <span class="text-ink font-bold">{{ $restaurant->cuisine }}</span>
            </div>
        </div>

        <!-- MAIN RESTAURANT PROFILE CARD (Clean, White Surface, Warm Mediterranean) -->
        <div class="bg-surface rounded-2xl border border-warm p-6 sm:p-8 shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- PHOTO (Sharp, Natural, High-Res, No dark wash) -->
                <div class="lg:col-span-6 w-full">
                    <div class="aspect-[16/10] w-full rounded-2xl overflow-hidden bg-sand border border-warm relative shadow-xs">
                        <img src="{{ $restaurant->image }}" 
                             alt="{{ $restaurant->name }}" 
                             class="w-full h-full object-cover">

                        <!-- Rating Badge (Top Left) -->
                        <div class="absolute top-3.5 left-3.5">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-md text-xs font-black bg-ink text-white shadow-xs">
                                <x-ico name="star" filled class="w-3.5 h-3.5 text-star" />
                                <span>{{ number_format($restaurant->rating, 1) }}</span>
                                <span class="text-stone-300 font-normal text-[11px]">({{ $restaurant->reviews_count }})</span>
                            </span>
                        </div>

                        <!-- Open Status (Top Right) -->
                        <div class="absolute top-3.5 right-3.5">
                            @if($restaurant->is_open)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-bold bg-open text-white shadow-xs">
                                    <span class="w-2 h-2 rounded-full bg-white"></span>
                                    Açık • {{ $restaurant->opening_hours }}
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-md text-xs font-bold bg-ink text-stone-300 shadow-xs border border-stone-700/50">
                                    Kapalı
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- RESTAURANT ESSENTIALS & ACTIONS -->
                <div class="lg:col-span-6 flex flex-col justify-between h-full space-y-6">
                    <div>
                        <!-- Badges (SVG icons instead of emoji) -->
                        <div class="flex items-center gap-2 text-xs mb-2.5 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md font-bold bg-sand text-ink border border-warm">
                                <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
                                {{ $restaurant->city->name }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md font-bold bg-sand text-muted border border-warm">
                                <x-ico name="tag" class="w-3.5 h-3.5" />
                                {{ $restaurant->cuisine }}
                            </span>
                            <span class="px-2.5 py-1 rounded-md font-bold font-mono bg-sand text-ink border border-warm">
                                {{ $restaurant->price_range }}
                            </span>
                        </div>

                        <!-- Restaurant Name -->
                        <h1 class="text-3xl sm:text-4xl font-extrabold text-ink tracking-tight leading-tight">
                            {{ $restaurant->name }}
                        </h1>

                        <!-- Bio / Description -->
                        <p class="text-sm text-muted mt-3 leading-relaxed font-normal">
                            {{ $restaurant->description }}
                        </p>

                        <!-- Quick Info Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-5 pt-5 border-t border-warm text-xs text-muted">
                            <div>
                                <span class="font-bold text-ink uppercase tracking-wider block text-[11px]">Adres</span>
                                <span class="font-medium text-ink mt-0.5 block leading-snug">{{ $restaurant->address }}</span>
                            </div>
                            <div>
                                <span class="font-bold text-ink uppercase tracking-wider block text-[11px]">Mesafe & Konum</span>
                                <span class="font-medium text-ink mt-0.5 block">Şehir Merkezine {{ $restaurant->distance }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- PRIMARY CALL TO ACTIONS -->
                    <div class="pt-4 border-t border-warm flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                           class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-xs text-center">
                            <x-ico name="book-open" class="w-5 h-5" />
                            <span>Dijital Menüyü ve Fiyatları Gör</span>
                            <span class="opacity-80 font-normal text-xs">({{ $restaurant->menuItems->count() }} Çeşit)</span>
                        </a>

                        @if($restaurant->phone)
                            <a href="tel:{{ $restaurant->phone }}"
                               class="inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-sm shadow-2xs text-center">
                                <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                                <span>{{ $restaurant->phone }}</span>
                            </a>
                        @endif

                        <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->latitude }},{{ $restaurant->longitude }}"
                           target="_blank"
                           rel="noopener"
                           aria-label="{{ $restaurant->name }} konumunu Google Haritalar'da aç"
                           class="inline-flex items-center justify-center p-3.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-sm shadow-2xs text-center"
                           title="Google Haritalar">
                            <x-ico name="map" class="w-5 h-5 text-terracotta" />
                        </a>
                    </div>

                </div>

            </div>
        </div>

        <!-- TWO COLUMN LAYOUT: HIGHLIGHTS & PRACTICAL DETAILS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT 8 COLS: Featured Dishes Highlights -->
            <div class="lg:col-span-8 space-y-8">
                
                @if(isset($featuredItems) && $featuredItems->isNotEmpty())
                    <div class="bg-surface rounded-2xl border border-warm p-6 sm:p-7 shadow-xs">
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-warm">
                            <div>
                                <h2 class="text-xl font-extrabold text-ink">
                                    Öne Çıkan Lezzetler
                                </h2>
                                <p class="text-xs text-muted mt-0.5 font-medium">Bu restoranda en çok tercih edilen menü çeşitleri</p>
                            </div>

                            <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                               class="text-xs font-bold text-terracotta hover:text-terracotta-dark uppercase tracking-wider">
                                Tüm Menü ({{ $restaurant->menuItems->count() }}) →
                            </a>
                        </div>

                        <!-- Highlights Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($featuredItems as $dish)
                                <x-menu-item-card :dish="$dish" :showMenuLink="true" :slug="$restaurant->slug" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- MEKAN ÖZELLİKLERİ -->
                <div class="bg-surface rounded-2xl border border-warm p-6 sm:p-7 shadow-xs">
                    <h3 class="text-base font-extrabold text-ink mb-3 pb-3 border-b border-warm">
                        Mekan Özellikleri & İmkanlar
                    </h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-bold text-ink">
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Açık Hava & Teras</span>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Ücretsiz Wi-Fi</span>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Kolay Otopark</span>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Kredi Kartı Geçerli</span>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Paket Servis</span>
                        </div>
                        <div class="flex items-center gap-2 p-3 rounded-xl bg-sand border border-warm">
                            <x-ico name="check" class="w-3.5 h-3.5 text-open shrink-0" />
                            <span>Rezervasyon İmkanı</span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT 4 COLS: Info Sidebar -->
            <div class="lg:col-span-4 space-y-6">

                <!-- ÇALIŞMA SAATLERİ & KONUM -->
                <div class="bg-surface rounded-2xl border border-warm p-6 shadow-xs space-y-5">
                    <h3 class="font-extrabold text-sm text-ink uppercase tracking-wider pb-2 border-b border-warm">
                        Çalışma Saatleri & İletişim
                    </h3>

                    <!-- Status Box -->
                    <div class="p-3.5 rounded-xl {{ $restaurant->is_open ? 'bg-emerald-50 border border-emerald-200/60' : 'bg-sand border border-warm' }} flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold {{ $restaurant->is_open ? 'text-open' : 'text-muted' }}">
                                {{ $restaurant->is_open ? 'Şu Anda Hizmet Veriyor' : 'Şu Anda Kapalı' }}
                            </span>
                            <p class="text-xs font-bold text-ink mt-0.5">
                                Her Gün: {{ $restaurant->opening_hours }}
                            </p>
                        </div>
                        <span class="w-3 h-3 rounded-full {{ $restaurant->is_open ? 'bg-open' : 'bg-stone-400' }}"></span>
                    </div>

                    <!-- Contact details -->
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-muted font-bold block text-[11px] uppercase tracking-wider">Adres</span>
                            <p class="text-ink font-semibold mt-0.5 leading-relaxed">{{ $restaurant->address }}</p>
                            <p class="text-muted mt-0.5">{{ $restaurant->city->name }}, Kuzey Kıbrıs</p>
                        </div>

                        @if($restaurant->phone)
                            <div class="pt-2 border-t border-warm">
                                <span class="text-muted font-bold block text-[11px] uppercase tracking-wider">Telefon</span>
                                <a href="tel:{{ $restaurant->phone }}" class="text-ink font-bold text-sm block mt-0.5 hover:text-terracotta">
                                    {{ $restaurant->phone }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Map Link Button -->
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->latitude }},{{ $restaurant->longitude }}"
                       target="_blank"
                       rel="noopener"
                       class="inline-flex w-full items-center justify-center gap-1.5 py-3 rounded-xl bg-ink text-white hover:bg-terracotta text-center font-bold text-xs uppercase tracking-wider shadow-xs">
                        <span>Google Haritalarda Aç</span>
                        <x-ico name="external" class="w-3.5 h-3.5" />
                    </a>
                </div>

                <!-- FAST ACCESS TO DIGITAL MENU -->
                <div class="bg-sand rounded-2xl border border-warm p-6 shadow-2xs text-center space-y-3">
                    <div class="w-10 h-10 rounded-full bg-terracotta text-white flex items-center justify-center mx-auto">
                        <x-ico name="book-open" class="w-5 h-5" />
                    </div>
                    <h4 class="font-extrabold text-ink text-sm">Menü ve Fiyat Listesi</h4>
                    <p class="text-xs text-muted leading-relaxed font-medium">
                        Tüm başlangıç, ana yemek, tatlı ve içecekleri güncel porsiyon fiyatlarıyla inceleyin.
                    </p>
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-block w-full py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs uppercase tracking-wider shadow-xs">
                        Menüyü İncele →
                    </a>
                </div>

            </div>

        </div>

        <!-- CIVARDAKİ RESTORANLAR -->
        @if($relatedRestaurants->isNotEmpty())
            <div class="pt-10 border-t border-warm">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-xl font-extrabold text-ink">
                            {{ $restaurant->city->name }} Bölgesindeki Diğer Mekanlar
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Aynı lokasyondaki alternatif restoran ve kafeler</p>
                    </div>

                    <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" 
                       class="text-xs font-bold text-terracotta hover:text-terracotta-dark uppercase tracking-wider">
                        Tümünü Gör →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedRestaurants as $rel)
                        <x-restaurant-card :restaurant="$rel" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

@endsection
