@extends('layouts.app')

@section('title', "Kıbrıs'ta Ne Yemek İstiyorsunuz? — Restoran & Dijital Menü Platformu")

@section('content')

    <!-- HERO SECTION -->
    <section class="relative bg-sand border-b border-warm pt-16 pb-20 overflow-hidden">
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
            
            <!-- Curated Location Pill -->
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-white border border-warm text-ink text-xs font-bold uppercase tracking-wider mb-6 shadow-2xs">
                <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                <span>Kuzey Kıbrıs Restoran & Dijital Menü Rehberi</span>
            </div>

            <!-- HERO TITLE -->
            <h1 class="text-3xl sm:text-5xl md:text-6xl font-extrabold text-ink tracking-tight leading-[1.1] uppercase">
                KIBRIS'TA NE YEMEK <br class="hidden sm:inline" />
                <span class="text-terracotta">İSTİYORSUNUZ?</span>
            </h1>

            <!-- HERO SUBTITLE -->
            <p class="mt-4 text-base sm:text-lg text-muted max-w-xl mx-auto font-normal leading-relaxed">
                Yakınınızdaki restoranları keşfedin, yemek çeşitlerine ve güncel porsiyon fiyatlarına anında göz atın.
            </p>

            <!-- SEARCH BAR (Wireframe: [ 🔍 Restoran, yemek veya mutfak ara... ] [ Ara ]) -->
            <form action="{{ route('restaurants.index') }}" method="GET" class="mt-8 sm:mt-10 max-w-2xl mx-auto">
                <input type="hidden" name="city" value="{{ $selectedCity->slug ?? 'girne' }}">
                @if($selectedCategorySlug)
                    <input type="hidden" name="category" value="{{ $selectedCategorySlug }}">
                @endif
                
                <div class="flex items-center bg-white rounded-2xl shadow-sm border border-warm p-2 focus-within:border-terracotta focus-within:ring-2 focus-within:ring-terracotta/15 transition-all">
                    <div class="pl-3.5 pr-2 text-muted">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" 
                           name="q" 
                           value="{{ $searchQuery ?? '' }}" 
                           placeholder="Restoran adı, yemek veya mutfak arayın (örn: Şeftali Kebabı, Burger, Pizza)..." 
                           class="w-full bg-transparent border-none text-ink text-sm sm:text-base placeholder-muted/70 focus:outline-none focus:ring-0 font-medium">
                    <button type="submit" 
                            class="px-6 sm:px-8 py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm uppercase tracking-wider transition-colors shrink-0 shadow-xs">
                        Ara
                    </button>
                </div>
            </form>

            <!-- LOCATION & QUICK MAP SHORTCUT (Wireframe: [ 📍 Girne ]  [ Haritada Ara ]) -->
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 text-xs sm:text-sm">
                <!-- Location indicator -->
                <div class="inline-flex items-center gap-2 bg-white border border-warm px-4 py-2 rounded-full text-ink shadow-2xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-open"></span>
                    <span class="text-muted">Konum:</span>
                    <span class="font-extrabold text-ink">{{ $selectedCity->name ?? 'Girne' }}</span>
                </div>

                <!-- [Haritada Ara] button -->
                <a href="#harita" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-white hover:bg-sand border border-warm text-ink hover:text-terracotta font-bold transition-colors shadow-2xs">
                    <svg class="w-4 h-4 text-terracotta" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span>Haritada Ara</span>
                </a>
            </div>

            <!-- City quick switch pills -->
            <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs">
                @foreach($cities as $city)
                    <x-city-pill 
                        :active="($selectedCity->slug ?? '') == $city->slug"
                        :href="'?city='.$city->slug.($searchQuery ? '&q='.$searchQuery : '').($selectedCategorySlug ? '&category='.$selectedCategorySlug : '')">
                        {{ $city->name }}
                    </x-city-pill>
                @endforeach
            </div>

        </div>
    </section>

    <!-- CATEGORIES SECTION (Wireframe: Kategoriler | Pizza, Burger, Cafe, Sushi, Steak... →) -->
    <section id="kategoriler" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 border-b border-warm">
        <div class="flex items-center justify-between mb-5">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-muted block">Kategoriler</span>
                <h2 class="text-xl sm:text-2xl font-black text-ink mt-0.5">
                    Mutfak Türüne Göre Seçin
                </h2>
            </div>

            <div class="flex items-center gap-3">
                @if($selectedCategorySlug)
                    <a href="?city={{ $selectedCity->slug ?? 'girne' }}" class="text-xs font-bold text-muted hover:text-ink bg-surface border border-warm px-3 py-1.5 rounded-lg transition-colors">
                        ✕ Filtreyi Kaldır
                    </a>
                @endif
                <a href="{{ route('categories') }}" class="text-xs font-bold text-terracotta hover:text-terracotta-dark hover:underline">
                    Tüm Kategoriler →
                </a>
            </div>
        </div>

        <!-- Horizontal scrollable categories -->
        <div class="flex items-center gap-2.5 overflow-x-auto pb-2 hide-scrollbar">
            <!-- All pill -->
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

            <div class="px-2 text-muted text-sm hidden sm:block select-none font-bold">→</div>
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
