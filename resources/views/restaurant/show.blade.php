@extends('layouts.app')

@section('title', $restaurant->name . ' — Menü, Konum ve Çalışma Saatleri | AdaMenü Kıbrıs')

@section('content')

@php
    $primary = $restaurant->branches->firstWhere('is_main', true) ?? $restaurant->branches->first();
    $hasMultipleBranches = $restaurant->branches->count() > 1;
    $todayOpen = $restaurant->isOpenNow();
    $address = ($primary->address ?? $restaurant->address) ?: '';
    $allReviews = $restaurant->branches->flatMap->reviews;
    $firstBranchId = ($primary ?? $restaurant->branches->first())->id ?? null;

    $days = [
        'monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba',
        'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar',
    ];
    $todayKey = strtolower(now()->format('l'));
    $schedule = $primary ?? $restaurant;
    $weekly = is_array($schedule->weekly_hours ?? null) ? $schedule->weekly_hours : ($restaurant->weekly_hours ?? null);
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    <!-- Utilities -->
    <div class="flex items-center justify-between py-5">
        <a href="{{ route('restaurants.index') }}"
           class="inline-flex items-center gap-2 text-sm font-bold text-muted hover:text-terracotta">
            <x-ico name="chevron-right" class="w-4 h-4 rotate-180" />
            Restoranlar
        </a>
        @if($restaurant->phone)
            <a href="tel:{{ $restaurant->phone }}"
               class="inline-flex items-center gap-2 text-sm font-semibold text-ink hover:text-terracotta">
                <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                {{ $restaurant->phone }}
            </a>
        @endif
    </div>

    @if(session('success'))
        <p class="mb-4 flex items-center gap-2 text-sm font-semibold text-open" role="status">
            <x-ico name="check" class="w-4 h-4" />
            {{ session('success') }}
        </p>
    @endif

    <!-- ================= HERO / PHOTO GRID (Vilka / Airbnb Style) ================= -->
    @php
        $galleryImages = is_array($restaurant->gallery) ? array_values(array_filter($restaurant->gallery)) : [];
        $hasGallery = !empty($galleryImages);
        $totalPhotos = ($hasGallery ? count($galleryImages) : 0) + ($restaurant->image ? 1 : 0);
        $allPhotos = array_filter(array_merge([$restaurant->image], array_map(fn($img) => \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img), $galleryImages)));
    @endphp

    <div x-data="{ 
            activeImage: null, 
            isOpen: false,
            currentIndex: 0,
            photos: {{ json_encode(array_values($allPhotos)) }},
            openModal(idx) {
                this.currentIndex = idx;
                this.activeImage = this.photos[idx];
                this.isOpen = true;
            },
            next() {
                if (this.currentIndex < this.photos.length - 1) {
                    this.currentIndex++;
                } else {
                    this.currentIndex = 0;
                }
                this.activeImage = this.photos[this.currentIndex];
            },
            prev() {
                if (this.currentIndex > 0) {
                    this.currentIndex--;
                } else {
                    this.currentIndex = this.photos.length - 1;
                }
                this.activeImage = this.photos[this.currentIndex];
            }
         }" 
         class="relative">
        
        @if(!$hasGallery || count($galleryImages) === 0)
            <!-- Single Cover Photo -->
            <div class="rounded-2xl overflow-hidden border border-warm bg-sand">
                <div class="aspect-[16/9] sm:aspect-[21/9]">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                </div>
            </div>
        @elseif(count($galleryImages) === 1)
            <!-- 2-Photo Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rounded-2xl overflow-hidden border border-warm bg-sand p-1">
                <button type="button" @click="openModal(0)" class="relative aspect-4/3 sm:aspect-auto sm:h-80 md:h-96 rounded-xl overflow-hidden group focus:outline-none">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300">
                </button>
                <button type="button" @click="openModal(1)" class="relative aspect-4/3 sm:aspect-auto sm:h-80 md:h-96 rounded-xl overflow-hidden group focus:outline-none">
                    <img src="{{ $allPhotos[1] ?? $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300">
                    <div class="absolute bottom-3 right-3">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink/80 hover:bg-ink text-white text-xs font-bold backdrop-blur-sm shadow-md">
                            <x-ico name="camera" class="w-4 h-4" />
                            2 Fotoğrafı Gör
                        </span>
                    </div>
                </button>
            </div>
        @elseif(count($galleryImages) === 2 || count($galleryImages) === 3)
            <!-- 3-Photo Grid (Vilka layout: 1 Main + 2 Stacked on right) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 rounded-2xl overflow-hidden border border-warm bg-sand/40 p-1">
                <button type="button" @click="openModal(0)" class="relative sm:col-span-2 aspect-[16/10] sm:aspect-auto sm:h-84 md:h-[400px] rounded-xl overflow-hidden group focus:outline-none">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300">
                </button>
                <div class="grid grid-cols-2 sm:grid-cols-1 gap-2.5 sm:h-84 md:h-[400px]">
                    <button type="button" @click="openModal(1)" class="relative h-full aspect-4/3 sm:aspect-auto rounded-xl overflow-hidden group focus:outline-none">
                        <img src="{{ $allPhotos[1] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    </button>
                    <button type="button" @click="openModal(2)" class="relative h-full aspect-4/3 sm:aspect-auto rounded-xl overflow-hidden group focus:outline-none">
                        <img src="{{ $allPhotos[2] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute bottom-3 right-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink/80 hover:bg-ink text-white text-xs font-bold backdrop-blur-sm shadow-md">
                                <x-ico name="camera" class="w-3.5 h-3.5" />
                                Tümünü Gör ({{ $totalPhotos }})
                            </span>
                        </div>
                    </button>
                </div>
            </div>
        @else
            <!-- 5-Photo Grid (Vilka / Airbnb Layout: 1 Large Left + 4 Grid Right) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-2.5 rounded-2xl overflow-hidden border border-warm bg-sand/30 p-1">
                <!-- Large Main Photo (Col 1-2) -->
                <button type="button" @click="openModal(0)" class="relative md:col-span-2 aspect-[16/10] md:aspect-auto md:h-[420px] rounded-xl overflow-hidden group focus:outline-none">
                    <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-102 transition-transform duration-300">
                </button>
                
                <!-- 4 Smaller Photos in 2x2 Grid (Col 3-4) -->
                <div class="grid grid-cols-2 gap-2.5 md:col-span-2 md:h-[420px]">
                    @for($i = 1; $i <= 4; $i++)
                        @if(isset($allPhotos[$i]))
                            <button type="button" @click="openModal({{ $i }})" class="relative h-full aspect-4/3 md:aspect-auto rounded-xl overflow-hidden group focus:outline-none">
                                <img src="{{ $allPhotos[$i] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                
                                @if($i === 4)
                                    <div class="absolute inset-0 bg-ink/30 group-hover:bg-ink/40 transition-colors flex items-center justify-center p-2">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-surface text-ink font-bold text-xs shadow-lg">
                                            <x-ico name="camera" class="w-3.5 h-3.5 text-terracotta" />
                                            +{{ $totalPhotos - 4 }} Fotoğraf
                                        </span>
                                    </div>
                                @endif
                            </button>
                        @endif
                    @endfor
                </div>
            </div>
        @endif

        <!-- Floating All Photos Badge Button -->
        @if($totalPhotos > 1)
            <button type="button" 
                    @click="openModal(0)"
                    class="hidden sm:inline-flex absolute bottom-4 right-4 items-center gap-2 px-3.5 py-2 rounded-xl bg-surface/95 hover:bg-surface text-ink font-bold text-xs shadow-md border border-warm backdrop-blur-md transition-all hover:scale-102">
                <x-ico name="camera" class="w-4 h-4 text-terracotta" />
                <span>Tüm Fotoğraflar ({{ $totalPhotos }})</span>
            </button>
        @endif

        <!-- Fullscreen Gallery Lightbox Modal -->
        <template x-teleport="body">
            <div x-show="isOpen" 
                 x-cloak 
                 @keydown.escape.window="isOpen = false"
                 @keydown.arrow-right.window="next()"
                 @keydown.arrow-left.window="prev()"
                 class="fixed inset-0 z-[9999] bg-ink/95 backdrop-blur-md flex flex-col justify-between p-3 sm:p-6 select-none overflow-hidden h-screen w-screen">
                
                <!-- Modal Top Header Bar -->
                <div class="flex items-center justify-between text-white pb-3 border-b border-white/10 w-full shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-sm sm:text-base text-white tracking-tight">{{ $restaurant->name }}</span>
                        <span class="text-xs text-stone-300 bg-white/10 px-2.5 py-1 rounded-full font-mono" x-text="(currentIndex + 1) + ' / ' + photos.length"></span>
                    </div>
                    <button type="button" 
                            @click="isOpen = false" 
                            class="text-white hover:text-white font-bold text-sm flex items-center gap-1.5 bg-white/15 hover:bg-white/25 px-4 py-2 rounded-xl transition-all cursor-pointer shadow-lg focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Kapat</span>
                    </button>
                </div>

                <!-- Modal Center Image Area with Navigation Buttons -->
                <div class="relative flex-1 w-full flex items-center justify-center min-h-0 py-2 sm:py-4 px-2 sm:px-16"
                     @click.self="isOpen = false">
                    
                    <!-- Prev Button -->
                    <button type="button" 
                            x-show="photos.length > 1"
                            @click.stop="prev()" 
                            aria-label="Önceki Fotoğraf"
                            class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-20 p-3 sm:p-4 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 transition-all hover:scale-110 active:scale-95 shadow-2xl focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <!-- Active Image Display -->
                    <div class="h-full w-full flex items-center justify-center p-2">
                        <img :src="activeImage" 
                             alt="{{ $restaurant->name }}" 
                             class="max-h-[68vh] sm:max-h-[72vh] max-w-[95vw] sm:max-w-[85vw] w-auto h-auto rounded-xl object-contain shadow-2xl transition-all duration-200 border border-white/10">
                    </div>

                    <!-- Next Button -->
                    <button type="button" 
                            x-show="photos.length > 1"
                            @click.stop="next()" 
                            aria-label="Sonraki Fotoğraf"
                            class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-20 p-3 sm:p-4 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 transition-all hover:scale-110 active:scale-95 shadow-2xl focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Bottom Thumbnails Strip -->
                <div x-show="photos.length > 1" class="w-full shrink-0 flex items-center justify-center gap-2 overflow-x-auto py-2 hide-scrollbar">
                    <template x-for="(p, i) in photos" :key="i">
                        <button type="button" 
                                @click.stop="currentIndex = i; activeImage = p" 
                                :class="currentIndex === i ? 'ring-2 ring-terracotta scale-105 opacity-100' : 'opacity-40 hover:opacity-80'"
                                class="h-12 sm:h-14 aspect-4/3 rounded-lg overflow-hidden shrink-0 transition-all focus:outline-none cursor-pointer bg-stone-800">
                            <img :src="p" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- ================= RESTAURANT HEADER (Clean Flat Typography & Actions) ================= -->
    <header class="mt-8 pb-6 border-b border-warm/80">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
            <div class="min-w-0">
                <div class="flex items-center gap-2 text-xs font-semibold text-muted mb-2">
                    <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" class="hover:text-terracotta">{{ $restaurant->city->name }}</a>
                    <span>•</span>
                    <span>{{ $restaurant->cuisine }}</span>
                    @if($restaurant->price_range)
                        <span>•</span>
                        <span class="font-mono text-terracotta">{{ $restaurant->price_range }}</span>
                    @endif
                </div>

                <div class="flex flex-wrap items-baseline gap-4">
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-semibold text-ink tracking-tight">
                        {{ $restaurant->name }}
                    </h1>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sand border border-warm text-sm font-bold text-ink">
                        <x-ico name="star" filled class="w-4 h-4 text-star" />
                        <span>{{ number_format($restaurant->rating, 1) }}</span>
                        <span class="text-xs text-muted font-normal">({{ $restaurant->reviews_count }} değerlendirme)</span>
                    </div>
                </div>

                @if($address)
                    <p class="mt-2 text-xs sm:text-sm text-muted flex items-center gap-1.5">
                        <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta shrink-0" />
                        <span>{{ $address }}</span>
                    </p>
                @endif
            </div>

            <!-- Primary CTAs -->
            <div class="flex items-center gap-3 shrink-0">
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" 
                       class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-sand hover:bg-surface border border-warm text-ink font-bold text-xs sm:text-sm shadow-xs transition-colors">
                        <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                        <span>{{ $restaurant->phone }}</span>
                    </a>
                @endif
                <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs sm:text-sm shadow-xs transition-all hover:scale-102">
                    <x-ico name="book-open" class="w-4 h-4" />
                    <span>Dijital Menü</span>
                </a>
            </div>
        </div>
    </header>

    <!-- ================= MAIN CONTENT & SIDEBAR ================= -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">

        <!-- Left Column: Flat Clean Editorial Content (8 Cols) -->
        <div class="lg:col-span-8 space-y-12">
            
            <!-- 1. Mekan Hakkında -->
            <section class="space-y-4">
                <h2 class="text-lg font-bold text-ink tracking-tight flex items-center gap-2">
                    <span>Mekan Hakkında</span>
                </h2>
                <p class="text-sm sm:text-base text-ink/80 leading-relaxed font-normal">
                    {{ $restaurant->description ?: 'Misafirlerimize eşsiz lezzetler ve samimi bir atmosfer sunuyoruz.' }}
                </p>

                <!-- Hızlı Özellik Etiketleri -->
                <div class="pt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-warm text-xs font-semibold text-ink">
                        <span class="w-1.5 h-1.5 rounded-full bg-terracotta"></span>
                        {{ $restaurant->cuisine }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface border border-warm text-xs font-semibold text-ink">
                        <x-ico name="map-pin" class="w-3.5 h-3.5 text-muted" />
                        {{ $restaurant->city->name }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg {{ $todayOpen ? 'bg-emerald-50 text-open border border-emerald-200/60' : 'bg-rose-50 text-rose-700 border border-rose-200/60' }} text-xs font-bold">
                        <span class="w-2 h-2 rounded-full {{ $todayOpen ? 'bg-open animate-pulse' : 'bg-rose-500' }}"></span>
                        {{ $todayOpen ? 'Şu An Açık' : 'Şu An Kapalı' }}
                    </span>
                </div>
            </section>

            <!-- 2. Öne Çıkan Lezzetler -->
            @if($featuredItems->isNotEmpty())
                <section class="pt-8 border-t border-warm/80 space-y-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-bold text-ink tracking-tight">Öne Çıkan Lezzetler</h2>
                            <p class="text-xs text-muted">Şefin tavsiyeleri ve misafirlerin en çok tercih ettiği tatlar</p>
                        </div>
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:text-terracotta-dark">
                            <span>Tüm Menü</span>
                            <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @foreach($featuredItems as $dish)
                            <x-menu-item-card :dish="$dish" :showMenuLink="false" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 3. Değerlendirmeler & Yorumlar -->
            <section class="pt-8 border-t border-warm/80 space-y-6" x-data="{ showForm: false, rating: 5 }">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-ink tracking-tight">Değerlendirmeler</h2>
                        <p class="text-xs text-muted mt-0.5">{{ number_format($restaurant->rating, 1) }} / 5.0 ({{ $restaurant->reviews_count }} yorum)</p>
                    </div>
                    <button type="button" @click="showForm = !showForm"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-xs shadow-xs transition-colors">
                        <x-ico name="pencil" class="w-3.5 h-3.5" />
                        <span x-text="showForm ? 'Formu Kapat' : 'Değerlendirme Yaz'"></span>
                    </button>
                </div>

                <!-- Review Form -->
                <form id="review-form" x-show="showForm" x-cloak method="POST"
                      action="{{ $firstBranchId ? route('branches.reviews.store', $firstBranchId) : '#' }}"
                      class="p-5 rounded-2xl bg-surface border border-warm shadow-xs space-y-4">
                    @csrf
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-ink">Puanınız:</span>
                        <template x-for="s in [1,2,3,4,5]" :key="s">
                            <button type="button" @click="rating = s" :aria-label="'Puan ' + s"
                                    :class="s <= rating ? 'text-star' : 'text-muted/30'" class="focus:outline-none transition-transform hover:scale-110">
                                <x-ico name="star" filled class="w-5 h-5" />
                            </button>
                        </template>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="review-author" class="block text-xs font-bold text-muted mb-1">Adınız / Rumuz</label>
                            <input id="review-author" type="text" name="author_name" placeholder="Anonim misafir"
                                   class="w-full px-3.5 py-2 rounded-xl bg-sand border border-warm text-xs text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60">
                        </div>
                        <div>
                            <label for="review-branch" class="block text-xs font-bold text-muted mb-1">Şube</label>
                            @if($hasMultipleBranches)
                                <select id="review-branch"
                                        @change="document.getElementById('review-form').action = $event.target.selectedOptions[0].dataset.url"
                                        class="w-full px-3.5 py-2 rounded-xl bg-sand border border-warm text-xs text-ink focus:outline-none focus:border-terracotta">
                                    @foreach($restaurant->branches as $b)
                                        <option value="{{ $b->id }}" data-url="{{ route('branches.reviews.store', $b->id) }}" {{ $b->is_main ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="py-2 text-xs font-semibold text-ink">{{ $primary->name }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="review-comment" class="block text-xs font-bold text-muted mb-1">Yorumunuz</label>
                        <textarea id="review-comment" name="comment" rows="3"
                                  placeholder="Lezzet, servis ve ortam nasıldı?"
                                  class="w-full px-3.5 py-2 rounded-xl bg-sand border border-warm text-xs text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60 resize-none"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-1">
                        <button type="button" @click="showForm = false" class="px-3.5 py-2 rounded-lg text-xs font-bold text-muted hover:text-ink">İptal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold shadow-xs">Gönder</button>
                    </div>
                </form>

                <!-- Reviews List -->
                @if($allReviews->isEmpty())
                    <p class="text-xs text-muted italic">Henüz değerlendirme yapılmamış.</p>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($allReviews->take(4) as $rev)
                            <article class="p-4 rounded-xl bg-surface border border-warm flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-sand text-ink font-bold text-xs flex items-center justify-center border border-warm">
                                                {{ mb_substr($rev->author_name ?: 'A', 0, 1) }}
                                            </div>
                                            <span class="font-bold text-xs text-ink">{{ $rev->author_name ?: 'Anonim misafir' }}</span>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-ico name="star" filled class="w-3 h-3 {{ $i <= $rev->rating ? 'text-star' : 'text-stone-300' }}" />
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rev->comment)
                                        <p class="mt-2 text-xs text-ink/80 leading-relaxed font-normal">{{ $rev->comment }}</p>
                                    @endif
                                </div>
                                <p class="mt-3 text-[11px] text-muted">{{ $rev->created_at->diffForHumans() }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>

        <!-- Right Column: Sidebar (Working Hours + Map) (4 Cols) -->
        <aside class="lg:col-span-4 space-y-8 lg:sticky lg:top-24">
            
            <!-- Working Hours -->
            <div class="space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-warm/80">
                    <h3 class="text-sm font-bold text-ink">Çalışma Saatleri</h3>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full {{ $todayOpen ? 'bg-emerald-50 text-open border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                        {{ $todayOpen ? 'AÇIK' : 'KAPALI' }}
                    </span>
                </div>
                <ul class="divide-y divide-warm/50 text-xs">
                    @foreach($days as $key => $name)
                        @php
                            $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                            $isToday = $key === $todayKey;
                            $closed = is_array($cfg) && !empty($cfg['is_closed']);
                            $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . ' – ' . $cfg['close'] : null;
                            $time = $closed ? 'Kapalı' : ($range ?? ($schedule->opening_hours ?? '10:00 – 23:00'));
                        @endphp
                        <li class="flex items-center justify-between py-2 {{ $isToday ? 'font-bold text-terracotta' : 'text-stone-600' }}">
                            <span class="flex items-center gap-1.5">
                                <span>{{ $name }}</span>
                                @if($isToday)
                                    <span class="text-[9px] uppercase px-1 py-0.2 rounded bg-terracotta text-white font-bold">Bugün</span>
                                @endif
                            </span>
                            <span class="{{ $closed ? 'italic text-stone-400' : 'font-mono text-ink' }}">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Location & Map -->
            <div class="space-y-3 pt-6 border-t border-warm/80">
                <h3 class="text-sm font-bold text-ink">Konum & Ulaşım</h3>
                <div class="h-44 rounded-xl overflow-hidden border border-warm relative shadow-2xs"
                     x-data="{ init() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                         const m = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                         L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(m);
                         L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: L.divIcon({ className: 'custom-pin', html: '<div style=\'background:#E85D3F;color:#fff;padding:3px 7px;border-radius:9999px;font-weight:800;font-size:10px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.2);\'>★</div>', iconSize: [26,20], iconAnchor: [13,10] }) }).addTo(m);
                     }); } }" x-init="init()"></div>
                
                @if($address)
                    <p class="text-xs text-muted leading-relaxed font-normal">{{ $address }}</p>
                @endif
                
                <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-terracotta hover:text-terracotta-dark">
                    <x-ico name="map-pin" class="w-3.5 h-3.5" />
                    <span>Google Haritalar'da Yol Tarifi Al →</span>
                </a>
            </div>

        </aside>

    </div>

    <!-- ================= RELATED ================= -->
    @if($relatedRestaurants->isNotEmpty())
        <section class="mt-10">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-xl font-extrabold text-ink">{{ $restaurant->city->name }} çevresindekiler</h2>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
                   class="inline-flex items-center gap-1 text-sm font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                    Tümünü gör
                    <x-ico name="chevron-right" class="w-4 h-4" />
                </a>
            </div>
            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedRestaurants as $rel)
                    <x-restaurant-card :restaurant="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>

@endsection
