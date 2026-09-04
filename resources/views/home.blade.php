@extends('layouts.app')

@section('title', "Kıbrıs'ta Ne Yemek İstiyorsunuz? — Restoran & Dijital Menü Platformu")

@section('content')

@php
    // Editorial hero anchors on the most appetizing thing: a real restaurant's dish photo.
    $heroRest = ($popularRestaurants->isNotEmpty())
        ? $popularRestaurants->first()
        : ($nearbyRestaurants->isNotEmpty() ? $nearbyRestaurants->first() : null);
    $citySlugNow = $selectedCity->slug ?? 'girne';
@endphp

    <!-- HERO SECTION (editorial, image-anchored two-column) -->
    <section class="bg-sand border-b border-warm overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 pb-16 sm:pt-20 sm:pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-center">

                <!-- LEFT: intro + search -->
                <div class="lg:col-span-7">
                    <div class="flex items-center gap-2.5 mb-5">
                        <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                        <span class="text-xs sm:text-sm font-bold text-ink tracking-wide">Kuzey Kıbrıs Yeme-İçme Rehberi</span>
                    </div>

                    <h1 class="text-4xl sm:text-5xl xl:text-[3.35rem] font-black text-ink tracking-tight leading-[1.08]">
                        Bugün ne yiyecekseniz,<br class="hidden sm:block">
                        güncel fiyatıyla bakın.
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-muted max-w-lg leading-relaxed">
                        Girne'den Gazimağusa'ya restoran, meyhane ve kafeleri keşfedin; Şeftali Kebabı'ndan deniz mahsullerine doğrulanmış dijital menülere anında ulaşın.
                    </p>

                    <!-- SEARCH -->
                    <form action="{{ route('restaurants.index') }}" method="GET" class="mt-8 max-w-xl">
                        <input type="hidden" name="city" value="{{ $citySlugNow }}">
                        @if($selectedCategorySlug)
                            <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
                        @endif
                        <label for="hero-search" class="sr-only">Restoran, yemek veya mutfak ara</label>
                        <div class="flex items-center bg-surface rounded-2xl border border-warm p-1.5 pl-4 shadow-xs focus-within:border-terracotta focus-within:ring-2 focus-within:ring-terracotta/15">
                            <svg class="w-5 h-5 text-muted shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input id="hero-search"
                                   type="text"
                                   name="q"
                                   value="{{ $searchQuery ?? '' }}"
                                   placeholder="Restoran adı, yemek veya mutfak arayın…"
                                   class="w-full bg-transparent px-3 py-2.5 border-none text-ink text-sm sm:text-base placeholder-muted/70 focus:outline-none focus:ring-0 font-medium">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shrink-0 shadow-xs">
                                Ara
                            </button>
                        </div>
                    </form>

                    <!-- QUICK CITIES + MAP LINK -->
                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center gap-4">
                        <div class="flex items-center gap-2 flex-wrap text-xs">
                            <span class="text-muted font-bold mr-1">Hızlı seçim:</span>
                            @foreach($cities as $city)
                                <x-city-pill
                                    :active="($selectedCity->slug ?? '') == $city->slug"
                                    :href="'?city='.$city->slug.($searchQuery ? '&q='.$searchQuery : '').($selectedCategorySlug ? '&category='.$selectedCategorySlug : '')">
                                    {{ $city->name }}
                                </x-city-pill>
                            @endforeach
                        </div>
                        <a href="#harita" class="inline-flex items-center gap-1.5 text-sm font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                            <x-ico name="map" class="w-4 h-4" />
                            <span>Haritada Keşfet</span>
                        </a>
                    </div>
                </div>

                <!-- RIGHT: featured editorial card (the most characteristic thing in the subject's world) -->
                <div class="lg:col-span-5">
                    @if($heroRest)
                        <div class="group bg-surface rounded-3xl border border-warm overflow-hidden shadow-sm">
                            <div class="relative aspect-[4/3] overflow-hidden bg-sand">
                                <img src="{{ $heroRest->image }}"
                                     alt="{{ $heroRest->name }} öne çıkan yemek"
                                     class="w-full h-full object-cover">
                                <div class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-extrabold bg-ink text-white shadow-xs">
                                    <x-ico name="star" filled class="w-3 h-3 text-star" />
                                    <span>{{ number_format($heroRest->rating, 1) }}</span>
                                </div>
                                @if($heroRest->is_open)
                                    <div class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-open text-white shadow-xs">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                        Açık
                                    </div>
                                @endif
                            </div>
                            <div class="p-5 sm:p-6">
                                <div class="text-[11px] font-bold text-muted tracking-wider">{{ $heroRest->cuisine }} • {{ $heroRest->city->name }}</div>
                                <h2 class="mt-1 text-xl sm:text-2xl font-extrabold text-ink group-hover:text-terracotta">{{ $heroRest->name }}</h2>
                                <div class="mt-4 flex flex-wrap gap-2.5">
                                    <a href="{{ route('restaurant.menu', $heroRest->slug) }}"
                                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold shadow-xs">
                                        <x-ico name="book-open" class="w-4 h-4" />
                                        <span>Menüyü Gör</span>
                                    </a>
                                    <a href="{{ route('restaurant.show', $heroRest->slug) }}"
                                       class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-sand hover:bg-surface border border-warm text-ink text-xs font-bold">
                                        Mekan Detayı
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-surface rounded-3xl border border-warm p-10 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-sand text-terracotta flex items-center justify-center mx-auto">
                                <x-ico name="map" class="w-7 h-7" />
                            </div>
                            <p class="mt-4 text-sm text-muted">Yakında Kıbrıs sofraları burada.</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    <!-- CATEGORIES (surface browse control) -->
    <section id="kategoriler" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
        <div class="bg-surface rounded-2xl border border-warm p-5 sm:p-6 shadow-2xs">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-1.5 h-6 rounded-full bg-terracotta shrink-0"></span>
                    <h2 class="text-lg sm:text-xl font-extrabold text-ink truncate">Mutfak türüne göre seçin</h2>
                    @if($selectedCategorySlug)
                        <a href="?city={{ $selectedCity->slug ?? 'girne' }}"
                           class="inline-flex items-center gap-1 shrink-0 text-xs font-bold text-muted hover:text-ink bg-sand border border-warm px-2.5 py-1 rounded-lg">
                            <x-ico name="close" class="w-3 h-3" />
                            <span>Kaldır</span>
                        </a>
                    @endif
                </div>

                <a href="{{ route('categories') }}"
                   class="inline-flex items-center gap-1 shrink-0 text-xs font-bold text-terracotta hover:text-terracotta-dark">
                    <span>Tüm kategoriler</span>
                    <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar">
                <x-category-pill
                    :active="!$selectedCategorySlug"
                    :href="'?city='.($selectedCity->slug ?? 'girne')">
                    Tüm Mutfaklar
                </x-category-pill>

                @foreach($categories as $category)
                    <x-category-pill
                        :active="$selectedCategorySlug == $category->slug"
                        :href="'?city='.($selectedCity->slug ?? 'girne').'&category='.$category->slug.($searchQuery ? '&q='.$searchQuery : '')">
                        {{ $category->name }}
                    </x-category-pill>
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION 1: Yakınındaki Restoranlar (Wireframe: Yakınındaki restoranlar | Tümünü gör) -->
    <section id="kesfet" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <x-section-header 
            eyebrow="Konumunuza Göre" 
            title="Yakınınızdaki Restoranlar" 
            :badge="'('.($selectedCity->name ?? 'Girne').')'" 
            actionText="Tümünü gör" 
            :actionUrl="route('restaurants.index', ['city' => $selectedCity->slug ?? 'girne'])" 
        />

        @if($nearbyRestaurants->isEmpty())
            <x-empty-state 
                title="Mekan Bulunamadı" 
                message="Seçilen şehir veya kategoriye uygun mekan bulunamadı." 
                actionText="Filtreleri Sıfırla" 
                :actionUrl="route('home')" 
            />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
                @foreach($nearbyRestaurants as $rest)
                    <x-restaurant-card :restaurant="$rest" />
                @endforeach
            </div>
        @endif
    </section>

    <!-- SECTION 2: Popüler Restoranlar (Wireframe: 🔥 Popüler restoranlar | Tümünü gör) -->
    <section id="populer" class="bg-surface py-16 border-y border-warm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-header 
                eyebrow="Kıbrıs Favorileri" 
                title="Popüler Restoranlar" 
                actionText="Tümünü gör" 
                :actionUrl="route('restaurants.index')" 
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($popularRestaurants as $rest)
                    <x-restaurant-card :restaurant="$rest" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- SECTION 3: Haritada Keşfet (Wireframe: Haritada keşfet | MAP) -->
    <section id="harita" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <x-section-header 
            eyebrow="İnteraktif Konum" 
            title="Haritada Keşfet" 
            subtitle="Haritadaki pinlere tıklayarak mekanların fotoğraflarını, puanlarını ve dijital menülerini inceleyin." 
        />

        <x-map-section :restaurants="$mapData" :selectedCity="$selectedCity" />
    </section>

    <!-- SECTION 4: Yeni Restoranlar (Wireframe: Yeni restoranlar) -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 border-t border-warm">
        <x-section-header 
            eyebrow="Son Eklenenler" 
            title="Yeni Restoranlar" 
        />

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-7">
            @foreach($newRestaurants as $rest)
                <x-restaurant-card :restaurant="$rest" />
            @endforeach
        </div>
    </section>

@endsection
