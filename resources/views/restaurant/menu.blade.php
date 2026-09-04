@extends('layouts.app')

@section('title', $restaurant->name . " — Dijital Menü ve Fiyat Listesi | AdaMenü")

@section('content')

    <!-- RESTAURANT MENU HERO / HEADER -->
    <div class="bg-surface border-b border-warm py-6 sm:py-8 shadow-2xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                
                <div>
                    <a href="{{ route('restaurant.show', $restaurant->slug) }}" 
                       class="inline-flex items-center gap-1.5 text-xs font-bold text-muted hover:text-ink mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                        <span>Mekan Detayına Dön</span>
                    </a>

                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                            {{ $restaurant->name }}
                        </h1>
                        <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-sand text-terracotta border border-warm">
                            Dijital Menü
                        </span>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-muted font-medium mt-1.5">
                        <span class="font-bold text-star flex items-center gap-1">
                            <x-ico name="star" filled class="w-3 h-3" />
                            <span>{{ number_format($restaurant->rating, 1) }}</span>
                        </span>
                        <span>•</span>
                        <span>{{ $restaurant->city->name }}</span>
                        <span>•</span>
                        <span>{{ $restaurant->cuisine }}</span>
                    </div>
                </div>

                @if($restaurant->phone)
                    <div class="shrink-0">
                        <a href="tel:{{ $restaurant->phone }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs">
                            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                            <span>Sipariş / Rezervasyon: {{ $restaurant->phone }}</span>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- DIGITAL MENU INTERACTIVE CONTAINER (Alpine.js) -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8" 
         x-data="{
            activeCategory: 'all',
            searchQuery: '',
            
            matchesFilter(item, categoryId) {
                // Category match
                if (this.activeCategory !== 'all' && this.activeCategory != categoryId) {
                    return false;
                }

                // Search query match
                const searchLower = this.searchQuery.toLowerCase().trim();
                if (!searchLower) return true;

                const nameMatch = item.name.toLowerCase().includes(searchLower);
                const descMatch = item.desc && item.desc.toLowerCase().includes(searchLower);

                return nameMatch || descMatch;
            },

            categoryHasVisibleItems(catId, itemElements) {
                if (this.activeCategory !== 'all' && this.activeCategory != catId) {
                    return false;
                }
                return true;
            }
         }">

        <!-- CONTROLS: SEARCH & CATEGORY TABS -->
        <div class="sticky top-18 z-30 bg-sand/95 backdrop-blur-md pt-2 pb-4 space-y-3">
            
            <!-- SEARCH BAR -->
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <label for="menu-search" class="sr-only">Menüde ara</label>
                <input id="menu-search"
                       type="text"
                       x-model="searchQuery"
                       placeholder="Menüde yemek, tatlı veya içecek arayın..."
                       class="w-full pl-10 pr-10 py-3 bg-surface border border-warm rounded-xl text-sm text-ink placeholder-muted/70 focus:outline-none focus:border-terracotta font-medium shadow-2xs">

                <button type="button"
                        x-show="searchQuery"
                        @click="searchQuery = ''"
                        aria-label="Aramayı temizle"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-muted hover:text-ink">
                    <x-ico name="close" class="w-4 h-4" />
                </button>
            </div>

            <!-- CATEGORY SCROLL TABS -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar text-xs sm:text-sm">
                <button type="button" 
                        @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' 
                            ? 'bg-terracotta text-white font-bold shadow-xs' 
                            : 'bg-surface text-ink hover:border-muted font-semibold border border-warm'"
                        class="px-4 py-2 rounded-xl shrink-0 cursor-pointer">
                    Tüm Menü ({{ $restaurant->menuItems->count() }})
                </button>

                @foreach($restaurant->menuCategories as $cat)
                    <button type="button" 
                            @click="activeCategory = '{{ $cat->id }}'" 
                            :class="activeCategory === '{{ $cat->id }}' 
                                ? 'bg-terracotta text-white font-bold shadow-xs' 
                                : 'bg-surface text-ink hover:border-muted font-semibold border border-warm'"
                            class="px-4 py-2 rounded-xl shrink-0 cursor-pointer">
                        {{ $cat->name }} ({{ $cat->items->count() }})
                    </button>
                @endforeach
            </div>

        </div>

        <!-- MENU SECTIONS LIST -->
        <div class="space-y-12 mt-6">
            @foreach($restaurant->menuCategories as $cat)
                <div x-show="activeCategory === 'all' || activeCategory === '{{ $cat->id }}'" class="space-y-4">
                    
                    <!-- Section Heading -->
                    <div class="border-b border-warm pb-2.5 flex items-baseline justify-between">
                        <div class="flex items-center gap-2">
                            <h2 class="text-xl font-extrabold text-ink">
                                {{ $cat->name }}
                            </h2>
                            <span class="text-xs font-bold text-muted font-mono">
                                ({{ $cat->items->count() }})
                            </span>
                        </div>
                    </div>

                    <!-- Items 2-Col Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($cat->items as $dish)
                            <x-menu-item-card 
                                :dish="$dish"
                                :showMenuLink="false"
                                x-show="matchesFilter({
                                    name: {{ json_encode($dish->name) }},
                                    desc: {{ json_encode($dish->description ?? '') }}
                                }, '{{ $cat->id }}')"
                            />
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>

        <!-- MENU FOOTNOTE / ALLERGEN NOTICE -->
        <div class="mt-16 pt-8 border-t border-warm text-center text-xs text-muted space-y-1">
            <p class="font-bold text-ink">AdaMenü Kıbrıs Doğrulanmış Dijital Menü</p>
            <p>Fiyatlar işletme tarafından belirlenmektedir. Porsiyon ve içerik bilgileri için servis görevlisine danışabilirsiniz.</p>
        </div>

    </div>

@endsection
