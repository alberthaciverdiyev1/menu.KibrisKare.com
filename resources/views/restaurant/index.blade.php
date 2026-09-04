@extends('layouts.app')

@section('title', 'Kıbrıs Restoranları — Tüm Mekanlar ve Güncel Menüler | AdaMenü')

@section('content')

    <!-- TOP HEADER -->
    <div class="bg-sand border-b border-warm py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-baseline justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-ink tracking-tight">
                        @if($currentCity)
                            {{ $currentCity->name }} Restoranları
                        @elseif($currentCategory)
                            {{ $currentCategory->name }} Mekanları
                        @else
                            Kıbrıs Restoranları ve Menüleri
                        @endif
                    </h1>
                    <p class="text-sm text-muted mt-1 font-normal">
                        Toplam <strong class="text-ink font-bold">{{ $restaurants->total() }}</strong> mekan listeleniyor.
                    </p>
                </div>

                <a href="{{ route('map', ['city' => $citySlug]) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-warm hover:border-terracotta text-ink hover:text-terracotta font-bold text-xs self-start md:self-auto shadow-2xs">
                    <svg class="w-4 h-4 text-terracotta" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path></svg>
                    <span>Harita Görünümüne Geç</span>
                </a>
            </div>

            <!-- SEARCH & FILTER ROW -->
            <form action="{{ route('restaurants.index') }}" method="GET" class="mt-6 flex flex-col sm:flex-row gap-3">
                @if($citySlug)
                    <input type="hidden" name="city" value="{{ $citySlug }}">
                @endif
                @if($categorySlug)
                    <input type="hidden" name="category" value="{{ $categorySlug }}">
                @endif

                <div class="relative flex-grow">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <label for="restaurant-search" class="sr-only">Restoran ara</label>
                    <input id="restaurant-search"
                           type="text"
                           name="q"
                           value="{{ $search ?? '' }}"
                           placeholder="Mekan adı, mutfak veya adres arayın..."
                           class="w-full pl-10 pr-4 py-2.5 bg-white border border-warm rounded-xl text-sm text-ink placeholder-muted/70 focus:outline-none focus:border-terracotta focus:ring-1 focus:ring-terracotta font-medium shadow-2xs">
                </div>

                <div class="flex items-center gap-2">
                    <label for="sort-by" class="sr-only">Sıralama</label>
                    <select id="sort-by" name="sort" onchange="this.form.submit()" class="bg-white border border-warm rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-bold text-ink focus:outline-none focus:border-terracotta">
                        <option value="rating_desc" {{ ($sort ?? '') == 'rating_desc' ? 'selected' : '' }}>En Yüksek Puan</option>
                        <option value="reviews_desc" {{ ($sort ?? '') == 'reviews_desc' ? 'selected' : '' }}>En Çok Değerlendirilen</option>
                        <option value="name_asc" {{ ($sort ?? '') == 'name_asc' ? 'selected' : '' }}>İsim (A-Z)</option>
                    </select>

                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs uppercase tracking-wider shadow-xs">
                        Filtrele
                    </button>
                </div>
            </form>

            <!-- CITIES ROW -->
            <div class="mt-4 flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar text-xs">
                <span class="text-muted font-bold uppercase tracking-wider shrink-0 mr-1">Şehir:</span>
                <x-city-pill 
                    :active="!$citySlug || $citySlug == 'all'" 
                    :href="route('restaurants.index', array_merge(request()->except('city'), ['city' => 'all']))">
                    Tümü
                </x-city-pill>
                @foreach($cities as $c)
                    <x-city-pill 
                        :active="$citySlug == $c->slug" 
                        :count="$c->restaurants_count"
                        :href="route('restaurants.index', array_merge(request()->except('city'), ['city' => $c->slug]))">
                        {{ $c->name }}
                    </x-city-pill>
                @endforeach
            </div>

            <!-- CATEGORIES ROW -->
            <div class="mt-2.5 flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar text-xs">
                <span class="text-muted font-bold uppercase tracking-wider shrink-0 mr-1">Kategori:</span>
                <x-category-pill 
                    variant="ink"
                    :active="!$categorySlug" 
                    :href="route('restaurants.index', request()->except('category'))">
                    Tümü
                </x-category-pill>
                @foreach($categories as $cat)
                    <x-category-pill 
                        variant="ink"
                        :active="$categorySlug == $cat->slug" 
                        :count="$cat->restaurants_count"
                        :href="route('restaurants.index', array_merge(request()->except('category'), ['category' => $cat->slug]))">
                        {{ $cat->name }}
                    </x-category-pill>
                @endforeach
            </div>

        </div>
    </div>

    <!-- RESTAURANTS GRID -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @if($restaurants->isEmpty())
            <x-empty-state 
                title="Kriterlere Uygun Restoran Bulunamadı" 
                message="Lütfen farklı bir şehir veya kategori seçerek tekrar deneyin." 
                actionText="Filtreleri Sıfırla" 
                :actionUrl="route('restaurants.index')" 
            />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($restaurants as $rest)
                    <x-restaurant-card :restaurant="$rest" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-12">
                {{ $restaurants->links() }}
            </div>
        @endif
    </div>

@endsection
