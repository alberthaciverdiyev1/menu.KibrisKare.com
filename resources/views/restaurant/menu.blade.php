@extends('layouts.app')

@section('title', $restaurant->name . " — Dijital Menü ve Fiyatlar | AdaMenü")

@section('content')

    <!-- TOP BRANDING & NAVIGATION BAR -->
    <div class="bg-white border-b border-warm sticky top-18 z-30 shadow-xs">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
            
            <div class="flex items-center gap-3.5 min-w-0">
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="w-9 h-9 rounded-lg border border-warm hover:border-ink flex items-center justify-center text-ink shrink-0 bg-white" title="Mekan Detayına Dön">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </a>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 truncate">
                        <h1 class="font-extrabold text-lg sm:text-xl text-ink truncate">
                            {{ $restaurant->name }}
                        </h1>
                        <span class="hidden sm:inline-block text-xs font-bold uppercase tracking-wider text-terracotta bg-orange-50 px-2 py-0.5 rounded border border-orange-200/40">
                            Dijital Menü
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted font-semibold">
                        <span class="text-star font-bold">★ {{ number_format($restaurant->rating, 1) }}</span>
                        <span>•</span>
                        <span>{{ $restaurant->city->name }}</span>
                        <span>•</span>
                        <span class="text-ink font-bold">{{ $restaurant->price_range }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2.5 shrink-0">
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="text-xs font-bold text-ink hover:bg-sand px-3.5 py-2 rounded-lg border border-warm">
                    Mekan Bilgisi
                </a>
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="text-xs font-bold text-white bg-terracotta hover:bg-terracotta-dark px-4 py-2 rounded-lg shadow-xs">
                        📞 Ara
                    </a>
                @endif
            </div>

        </div>
    </div>

    <!-- DIGITAL MENU CONTAINER (Alpine.js powered) -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10" 
         x-data="{
            activeCategory: 'all',
            searchQuery: '',
            filterType: 'all',
            
            matchesFilter(item) {
                const searchLower = this.searchQuery.toLowerCase().trim();
                const matchesSearch = !searchLower || 
                    item.name.toLowerCase().includes(searchLower) || 
                    (item.desc && item.desc.toLowerCase().includes(searchLower));

                if (!matchesSearch) return false;

                if (this.filterType === 'chef') return item.is_chef;
                if (this.filterType === 'popular') return item.is_popular;
                if (this.filterType === 'vegetarian') return item.is_vegetarian;

                return true;
            }
         }">

        <!-- FILTER AND SEARCH CONTROLS -->
        <div class="mb-10 space-y-4 bg-white p-6 rounded-2xl border border-warm shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                
                <!-- Search input -->
                <div class="relative flex-grow max-w-md">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Menüde yemek veya içecek arayın..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-sand border border-warm rounded-xl text-sm text-ink placeholder-muted/70 focus:outline-none focus:border-terracotta focus:ring-1 focus:ring-terracotta font-medium">
                </div>

                <!-- Dietary pills -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar text-xs">
                    <button type="button" 
                            @click="filterType = 'all'" 
                            :class="filterType === 'all' ? 'bg-ink text-white font-extrabold shadow-xs' : 'bg-sand text-ink border border-warm hover:border-muted font-bold'"
                            class="px-3.5 py-2 rounded-lg shrink-0">
                        Tümü
                    </button>
                    <button type="button" 
                            @click="filterType = 'chef'" 
                            :class="filterType === 'chef' ? 'bg-terracotta text-white font-extrabold shadow-xs' : 'bg-sand text-ink border border-warm hover:border-muted font-bold'"
                            class="px-3.5 py-2 rounded-lg shrink-0">
                        ⭐ Şefin Tavsiyesi
                    </button>
                    <button type="button" 
                            @click="filterType = 'popular'" 
                            :class="filterType === 'popular' ? 'bg-terracotta text-white font-extrabold shadow-xs' : 'bg-sand text-ink border border-warm hover:border-muted font-bold'"
                            class="px-3.5 py-2 rounded-lg shrink-0">
                        🔥 Popüler
                    </button>
                    <button type="button" 
                            @click="filterType = 'vegetarian'" 
                            :class="filterType === 'vegetarian' ? 'bg-open text-white font-extrabold shadow-xs' : 'bg-sand text-ink border border-warm hover:border-muted font-bold'"
                            class="px-3.5 py-2 rounded-lg shrink-0">
                        🌱 Vejetaryen
                    </button>
                </div>

            </div>

            <!-- STICKY CATEGORY NAV TABS -->
            <div class="pt-3 border-t border-warm flex items-center gap-2 overflow-x-auto pb-1 hide-scrollbar text-xs sm:text-sm">
                <button type="button" 
                        @click="activeCategory = 'all'" 
                        :class="activeCategory === 'all' ? 'bg-terracotta text-white font-bold shadow-xs' : 'bg-sand text-ink hover:text-terracotta font-semibold border border-warm'"
                        class="px-3.5 py-2 rounded-lg shrink-0">
                    Tüm Menü ({{ $restaurant->menuItems->count() }})
                </button>

                @foreach($restaurant->menuCategories as $cat)
                    <button type="button" 
                            @click="activeCategory = '{{ $cat->id }}'" 
                            :class="activeCategory === '{{ $cat->id }}' ? 'bg-terracotta text-white font-bold shadow-xs' : 'bg-sand text-ink hover:text-terracotta font-semibold border border-warm'"
                            class="px-3.5 py-2 rounded-lg shrink-0">
                        {{ $cat->name }} ({{ $cat->items->count() }})
                    </button>
                @endforeach
            </div>

        </div>

        <!-- MENU SECTIONS -->
        <div class="space-y-12">
            @foreach($restaurant->menuCategories as $cat)
                <div x-show="activeCategory === 'all' || activeCategory === '{{ $cat->id }}'" class="space-y-6">
                    
                    <!-- Section Heading -->
                    <div class="border-b border-warm pb-3 flex items-baseline justify-between">
                        <h2 class="text-2xl font-black text-ink">
                            {{ $cat->name }}
                        </h2>
                        <span class="text-xs font-bold text-muted uppercase tracking-wider">
                            {{ $cat->items->count() }} Seçenek
                        </span>
                    </div>

                    <!-- Items Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        @foreach($cat->items as $dish)
                            <x-menu-item-card 
                                :dish="$dish"
                                x-show="matchesFilter({
                                    name: {{ json_encode($dish->name) }},
                                    desc: {{ json_encode($dish->description ?? '') }},
                                    is_chef: {{ $dish->is_chef_special ? 'true' : 'false' }},
                                    is_popular: {{ $dish->is_popular ? 'true' : 'false' }},
                                    is_vegetarian: {{ $dish->is_vegetarian ? 'true' : 'false' }}
                                })"
                            />
                        @endforeach
                    </div>

                </div>
            @endforeach
        </div>

        <!-- FOOTNOTE -->
        <div class="mt-16 pt-8 border-t border-warm text-center text-xs text-muted font-medium space-y-1">
            <p>Fiyatlar restoran tarafından güncellenmektedir. Vergiler dahildir.</p>
            <p>Gıda alerjiniz varsa lütfen sipariş öncesinde restoran personeline danışınız.</p>
        </div>

    </div>

@endsection
