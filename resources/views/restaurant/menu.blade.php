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
                        @if(isset($currentBranch) && $currentBranch)
                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-terracotta text-white shadow-2xs">
                                📍 {{ $currentBranch->name }}
                            </span>
                        @else
                            <span class="text-xs font-bold px-2.5 py-1 rounded-md bg-sand text-terracotta border border-warm">
                                Dijital Menü
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center gap-3 text-xs text-muted font-medium mt-1.5 flex-wrap">
                        <span class="font-bold text-star flex items-center gap-1">
                            <x-ico name="star" filled class="w-3 h-3" />
                            <span>{{ number_format($restaurant->rating, 1) }}</span>
                        </span>
                        <span>•</span>
                        <span>{{ $currentBranch ? ($currentBranch->city->name ?? $restaurant->city->name) : $restaurant->city->name }}</span>
                        <span>•</span>
                        <span>{{ $restaurant->cuisine }}</span>
                        @if(isset($currentBranch) && $currentBranch)
                            <span>•</span>
                            <span class="text-ink font-semibold">🕒 {{ $currentBranch->getTodayHours() }}</span>
                        @endif
                    </div>
                </div>

                @php
                    $phoneToCall = (isset($currentBranch) && $currentBranch->phone) ? $currentBranch->phone : $restaurant->phone;
                @endphp

                @if($phoneToCall)
                    <div class="shrink-0">
                        <a href="tel:{{ $phoneToCall }}"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs">
                            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                            <span>Sipariş / İletişim: {{ $phoneToCall }}</span>
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>

    <!-- DIGITAL MENU INTERACTIVE CONTAINER (Alpine.js) -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6" 
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

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center gap-3 shadow-2xs">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

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

        <!-- BRANCH REVIEW & RATING BOX (When viewing branch menu) -->
        @if(isset($currentBranch) && $currentBranch)
            <div class="mt-12 p-6 sm:p-8 rounded-3xl bg-surface border border-warm shadow-xs space-y-6"
                 x-data="{
                    selectedRating: 5,
                    showForm: false
                 }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-warm">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-muted block">{{ $currentBranch->name }}</span>
                        <h3 class="text-xl font-black text-ink mt-0.5">Deneyiminizi Puanlayın</h3>
                        <p class="text-xs text-muted mt-1">Bu şubedeki yemeğinizi veya servisinizi anonim olarak değerlendirebilirsiniz.</p>
                    </div>

                    <button type="button" 
                            @click="showForm = !showForm"
                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-xs transition-colors shrink-0">
                        <span x-text="showForm ? 'Kapat' : '★ Puan & Yorum Bırak'"></span>
                    </button>
                </div>

                <!-- Review Form -->
                <div x-show="showForm" x-collapse class="p-5 rounded-2xl bg-sand/60 border border-warm space-y-4">
                    <form action="{{ route('branches.reviews.store', $currentBranch->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Puanınız (1 - 5)</label>
                                <div class="flex items-center gap-2">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button" 
                                                @click="selectedRating = star"
                                                class="text-2xl transition-transform hover:scale-110 focus:outline-none">
                                            <span :class="star <= selectedRating ? 'text-amber-500' : 'text-stone-300'">★</span>
                                        </button>
                                    </template>
                                    <span class="text-xs font-bold text-ink font-mono ml-2" x-text="selectedRating + ' / 5'"></span>
                                </div>
                                <input type="hidden" name="rating" :value="selectedRating">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">İsim (İsteğe Bağlı)</label>
                                <input type="text" 
                                       name="author_name" 
                                       placeholder="Anonim Misafir veya Adınız" 
                                       class="w-full px-4 py-2.5 bg-surface border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Yorumunuz</label>
                            <textarea name="comment" 
                                      rows="2" 
                                      placeholder="Masa servisi, lezzet ve deneyiminiz nasıldı?" 
                                      class="w-full px-4 py-2.5 bg-surface border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium"></textarea>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-xs font-bold text-muted hover:text-ink">İptal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-terracotta text-white font-bold text-xs shadow-xs">Gönder</button>
                        </div>
                    </form>
                </div>

                <!-- Recent Reviews -->
                @if($currentBranch->reviews->isNotEmpty())
                    <div class="space-y-3 pt-2">
                        <span class="text-xs font-bold text-muted uppercase tracking-wider block">Son Yorumlar ({{ $currentBranch->reviews_count }})</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($currentBranch->reviews->take(4) as $rev)
                                <div class="p-3.5 rounded-2xl bg-sand/40 border border-warm/70 space-y-1.5">
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="font-bold text-ink">{{ $rev->author_name }}</span>
                                        <div class="flex items-center gap-0.5 text-amber-500">
                                            @for($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $rev->rating ? 'text-amber-500' : 'text-stone-300' }}">★</span>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rev->comment)
                                        <p class="text-xs text-ink/80">{{ $rev->comment }}</p>
                                    @endif
                                    <span class="text-[10px] text-muted block">{{ $rev->created_at->diffForHumans() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- MENU FOOTNOTE / ALLERGEN NOTICE -->
        <div class="mt-16 pt-8 border-t border-warm text-center text-xs text-muted space-y-1">
            <p class="font-bold text-ink">AdaMenü Kıbrıs Doğrulanmış Dijital Menü</p>
            <p>Fiyatlar işletme tarafından belirlenmektedir. Porsiyon ve içerik bilgileri için servis görevlisine danışabilirsiniz.</p>
        </div>

    </div>

@endsection
