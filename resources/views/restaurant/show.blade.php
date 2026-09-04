@extends('layouts.app')

@section('title', $restaurant->name . ' — Menü, Fiyatlar ve Konum | AdaMenü')

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

    // Gallery preparation
    $galleryImages = is_array($restaurant->gallery) ? array_values(array_filter($restaurant->gallery)) : [];
    $hasGallery = !empty($galleryImages);
    $totalPhotos = ($hasGallery ? count($galleryImages) : 0) + ($restaurant->image ? 1 : 0);
    $allPhotos = array_filter(array_merge([$restaurant->image], array_map(fn($img) => \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img), $galleryImages)));
    $allPhotos = array_values($allPhotos);
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-28 sm:pb-20">

    <!-- ================= BREADCRUMBS & TOP NAV ================= -->
    <nav class="flex items-center justify-between py-3 mb-2 text-xs font-medium text-muted">
        <ol class="flex items-center gap-1.5 flex-wrap">
            <li>
                <a href="{{ route('home') }}" class="hover:text-terracotta transition-colors">Ana Sayfa</a>
            </li>
            <li class="text-stone-300">/</li>
            <li>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" class="hover:text-terracotta transition-colors">{{ $restaurant->city->name }}</a>
            </li>
            <li class="text-stone-300">/</li>
            <li class="text-ink font-semibold truncate max-w-[200px] sm:max-w-none">{{ $restaurant->name }}</li>
        </ol>

        <div class="hidden sm:flex items-center gap-3">
            @if($restaurant->phone)
                <a href="tel:{{ $restaurant->phone }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-ink hover:text-terracotta transition-colors">
                    <x-ico name="phone" class="w-3.5 h-3.5 text-terracotta" />
                    <span>{{ $restaurant->phone }}</span>
                </a>
            @endif
        </div>
    </nav>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-open text-xs sm:text-sm font-semibold flex items-center gap-2.5" role="status">
            <x-ico name="check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ================= PHOTO GALLERY (Vilka / Airbnb Editorial Grid) ================= -->
    <div x-data="{
            isOpen: false,
            currentIndex: 0,
            photos: {{ json_encode($allPhotos) }},
            openModal(idx) {
                this.currentIndex = idx;
                this.isOpen = true;
                document.body.classList.add('overflow-hidden');
            },
            closeModal() {
                this.isOpen = false;
                document.body.classList.remove('overflow-hidden');
            },
            next() {
                this.currentIndex = (this.currentIndex + 1) % this.photos.length;
            },
            prev() {
                this.currentIndex = (this.currentIndex - 1 + this.photos.length) % this.photos.length;
            }
         }"
         class="relative mt-2">

        @if(!$hasGallery || count($allPhotos) <= 1)
            <!-- Single Full Photo -->
            <div class="relative w-full aspect-[16/9] sm:aspect-[21/9] max-h-[460px] rounded-3xl overflow-hidden bg-stone-200 group cursor-pointer"
                 @click="openModal(0)">
                <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-102">
                <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-surface/90 text-ink text-xs font-bold backdrop-blur-md">
                        <x-ico name="camera" class="w-4 h-4 text-terracotta" />
                        Büyük Boyutta Gör
                    </span>
                </div>
            </div>
        @elseif(count($allPhotos) === 2)
            <!-- 2 Photos Side by Side -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 aspect-[16/9] sm:aspect-[21/9] max-h-[460px]">
                <div @click="openModal(0)" class="relative h-full rounded-2xl sm:rounded-3xl overflow-hidden bg-stone-200 group cursor-pointer">
                    <img src="{{ $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                </div>
                <div @click="openModal(1)" class="relative h-full rounded-2xl sm:rounded-3xl overflow-hidden bg-stone-200 group cursor-pointer">
                    <img src="{{ $allPhotos[1] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                    <div class="absolute bottom-4 right-4">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink/85 hover:bg-ink text-white text-xs font-bold backdrop-blur-md transition-all shadow-md">
                            <x-ico name="camera" class="w-3.5 h-3.5" />
                            2 Fotoğraf
                        </span>
                    </div>
                </div>
            </div>
        @elseif(count($allPhotos) === 3 || count($allPhotos) === 4)
            <!-- 3 Photos: 1 Main (60%) + 2 Right Stacked (40%) -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 aspect-[16/9] sm:aspect-[21/9] max-h-[460px]">
                <div @click="openModal(0)" class="sm:col-span-8 h-full rounded-2xl sm:rounded-l-3xl overflow-hidden bg-stone-200 group cursor-pointer relative">
                    <img src="{{ $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                </div>
                <div class="hidden sm:grid sm:col-span-4 grid-rows-2 gap-3 h-full">
                    <div @click="openModal(1)" class="rounded-tr-3xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                        <img src="{{ $allPhotos[1] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                    </div>
                    <div @click="openModal(2)" class="rounded-br-3xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                        <img src="{{ $allPhotos[2] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                        <div class="absolute bottom-4 right-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-ink/85 hover:bg-ink text-white text-xs font-bold backdrop-blur-md transition-all shadow-md">
                                <x-ico name="camera" class="w-3.5 h-3.5" />
                                Tümünü Gör ({{ $totalPhotos }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- 5+ Photos Grid: 1 Large Left + 4 Grid Right (2x2) -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 aspect-[16/9] sm:aspect-[21/9] max-h-[460px]">
                <!-- Main Large Photo (Col 1-2) -->
                <div @click="openModal(0)" class="md:col-span-2 h-full rounded-2xl md:rounded-l-3xl overflow-hidden bg-stone-200 group cursor-pointer relative">
                    <img src="{{ $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                </div>

                <!-- 4 Smaller Grid Photos (Col 3-4) in a 2x2 subgrid -->
                <div class="hidden md:grid md:col-span-2 grid-cols-2 grid-rows-2 gap-3 h-full">
                    @for($i = 1; $i <= 4; $i++)
                        @if(isset($allPhotos[$i]))
                            @php
                                $cornerClass = '';
                                if ($i === 2) $cornerClass = 'rounded-tr-3xl';
                                if ($i === 4) $cornerClass = 'rounded-br-3xl';
                            @endphp
                            <div @click="openModal({{ $i }})" class="{{ $cornerClass }} overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                                <img src="{{ $allPhotos[$i] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">

                                @if($i === 4 && $totalPhotos > 5)
                                    <div class="absolute inset-0 bg-ink/40 group-hover:bg-ink/50 transition-colors flex items-center justify-center p-2">
                                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-surface/95 text-ink font-bold text-xs shadow-lg backdrop-blur-sm">
                                            <x-ico name="camera" class="w-4 h-4 text-terracotta" />
                                            +{{ $totalPhotos - 5 }} Fotoğraf
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endfor
                </div>
            </div>
        @endif

        <!-- Floating View All Photos Button (Mobile & Desktop) -->
        @if($totalPhotos > 1)
            <button type="button"
                    @click="openModal(0)"
                    class="absolute bottom-4 right-4 inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-surface/95 hover:bg-surface text-ink font-bold text-xs shadow-lg backdrop-blur-md transition-all hover:scale-105 active:scale-95 cursor-pointer">
                <x-ico name="camera" class="w-4 h-4 text-terracotta" />
                <span>Fotoğraflar ({{ $totalPhotos }})</span>
            </button>
        @endif

        <!-- Lightbox Modal (Teleported to body) -->
        <template x-teleport="body">
            <div x-show="isOpen"
                 x-cloak
                 @keydown.escape.window="closeModal()"
                 @keydown.arrow-right.window="next()"
                 @keydown.arrow-left.window="prev()"
                 class="fixed inset-0 z-[9999] bg-stone-950/95 backdrop-blur-lg flex flex-col justify-between p-4 sm:p-6 select-none overflow-hidden h-screen w-screen">

                <!-- Modal Header -->
                <div class="flex items-center justify-between text-white pb-3 border-b border-white/10 w-full shrink-0">
                    <div class="flex items-center gap-3">
                        <span class="font-bold text-sm sm:text-base text-white tracking-tight">{{ $restaurant->name }}</span>
                        <span class="text-xs text-stone-400 bg-white/10 px-2.5 py-1 rounded-full font-mono" x-text="(currentIndex + 1) + ' / ' + photos.length"></span>
                    </div>
                    <button type="button"
                            @click="closeModal()"
                            class="text-white hover:text-stone-200 font-bold text-sm flex items-center gap-1.5 bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl transition-all cursor-pointer shadow-lg focus:outline-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        <span>Kapat</span>
                    </button>
                </div>

                <!-- Modal Image Stage -->
                <div class="relative flex-1 w-full flex items-center justify-center min-h-0 py-4 px-2 sm:px-16"
                     @click.self="closeModal()">

                    <!-- Prev Arrow -->
                    <button type="button"
                            x-show="photos.length > 1"
                            @click.stop="prev()"
                            aria-label="Önceki Fotoğraf"
                            class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-20 p-3.5 sm:p-4 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 transition-all hover:scale-110 active:scale-95 shadow-2xl focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>

                    <!-- Active Image -->
                    <div class="h-full w-full flex items-center justify-center p-2">
                        <img :src="photos[currentIndex]"
                             alt="{{ $restaurant->name }}"
                             class="max-h-[68vh] sm:max-h-[74vh] max-w-[95vw] sm:max-w-[85vw] w-auto h-auto rounded-2xl object-contain shadow-2xl transition-all duration-200">
                    </div>

                    <!-- Next Arrow -->
                    <button type="button"
                            x-show="photos.length > 1"
                            @click.stop="next()"
                            aria-label="Sonraki Fotoğraf"
                            class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-20 p-3.5 sm:p-4 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 transition-all hover:scale-110 active:scale-95 shadow-2xl focus:outline-none cursor-pointer">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                </div>

                <!-- Bottom Thumbnails Strip -->
                <div x-show="photos.length > 1" class="w-full shrink-0 flex items-center justify-center gap-2 overflow-x-auto py-2 hide-scrollbar">
                    <template x-for="(p, i) in photos" :key="i">
                        <button type="button"
                                @click.stop="currentIndex = i"
                                :class="currentIndex === i ? 'ring-2 ring-terracotta scale-105 opacity-100' : 'opacity-40 hover:opacity-80'"
                                class="h-12 sm:h-14 aspect-4/3 rounded-xl overflow-hidden shrink-0 transition-all focus:outline-none cursor-pointer bg-stone-800">
                            <img :src="p" class="w-full h-full object-cover">
                        </button>
                    </template>
                </div>
            </div>
        </template>
    </div>

    <!-- ================= RESTAURANT MASTHEAD & ACTIONS ================= -->
    <header class="mt-8 pb-4">
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">

            <!-- Left Info Area -->
            <div class="min-w-0 flex-1">
                <!-- Title & Rating -->
                <div class="flex flex-wrap items-baseline gap-x-4 gap-y-2">
                    <h1 class="font-display text-3xl sm:text-4xl lg:text-5xl font-semibold text-ink tracking-tight">
                        {{ $restaurant->name }}
                    </h1>
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-surface text-sm font-bold text-ink shadow-2xs">
                        <x-ico name="star" filled class="w-4 h-4 text-star" />
                        <span>{{ number_format($restaurant->rating, 1) }}</span>
                        <span class="text-xs text-muted font-normal">({{ $restaurant->reviews_count }} değerlendirme)</span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}"
                       class="inline-flex items-center gap-2 px-5 py-3.5 rounded-2xl bg-surface hover:bg-sand text-ink font-bold text-sm shadow-2xs hover:shadow-xs transition-all">
                        <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                        <span>Ara / Rezervasyon</span>
                    </a>
                @endif

                <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                   class="inline-flex items-center gap-2 px-7 py-3.5 rounded-2xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-sm hover:shadow-md transition-all hover:scale-102">
                    <x-ico name="book-open" class="w-4 h-4" />
                    <span>Dijital Menüyü İncele</span>
                </a>
            </div>

        </div>
    </header>

    <!-- ================= MAIN CONTENT (8 Cols) & SIDEBAR (4 Cols) ================= -->
    <div class="mt-10 grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

        <!-- Left Column: Story, Featured Food, Reviews (8 Cols) -->
        <div class="lg:col-span-8 space-y-12">

            <!-- 1. Mekan Hikayesi / Açıklama -->
            <section class="space-y-3">
                <h2 class="text-xl font-bold text-ink tracking-tight font-display">Mekan Hakkında</h2>
                <div class="text-stone-700 text-sm sm:text-base leading-relaxed font-normal">
                    <p>{{ $restaurant->description ?: 'Misafirlerimize özenle hazırlanan lezzetler ve kaliteli bir atmosfer sunuyoruz.' }}</p>
                </div>
            </section>

            <!-- 2. Öne Çıkan Menü Seçenekleri -->
            @if($featuredItems->isNotEmpty())
                <section class="pt-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-ink tracking-tight font-display">Öne Çıkan Lezzetler</h2>
                            <p class="text-xs text-muted mt-0.5">En çok tercih edilen ve şefin özel spesiyalleri</p>
                        </div>
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                           class="inline-flex items-center gap-1.5 text-xs font-bold text-terracotta hover:text-terracotta-dark transition-colors">
                            <span>Tüm Menü</span>
                            <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($featuredItems as $dish)
                            <x-menu-item-card :dish="$dish" :showMenuLink="false" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 3. Şubeler (Birden fazla varsa) -->
            @if($hasMultipleBranches)
                <section class="pt-8 space-y-4">
                    <h2 class="text-xl font-bold text-ink tracking-tight font-display">Şubeler ({{ $restaurant->branches->count() }})</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($restaurant->branches as $branch)
                            <div class="p-4 rounded-2xl bg-surface shadow-2xs space-y-2">
                                <div class="flex items-center justify-between">
                                    <h3 class="font-bold text-sm text-ink">{{ $branch->name }}</h3>
                                    @if($branch->is_main)
                                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-sand text-terracotta">Merkez</span>
                                    @endif
                                </div>
                                <p class="text-xs text-muted">{{ $branch->address ?: $restaurant->address }}</p>
                                @if($branch->phone)
                                    <div class="pt-1">
                                        <a href="tel:{{ $branch->phone }}" class="inline-flex items-center gap-1 text-xs font-semibold text-terracotta hover:underline">
                                            <x-ico name="phone" class="w-3 h-3" />
                                            {{ $branch->phone }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 4. Değerlendirmeler & Yorumlar -->
            <section class="pt-8 space-y-6" x-data="{ showForm: false, rating: 5 }">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-ink tracking-tight font-display">Misafir Değerlendirmeleri</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="flex items-center text-star">
                                @for($i = 1; $i <= 5; $i++)
                                    <x-ico name="star" filled class="w-4 h-4 {{ $i <= round($restaurant->rating) ? 'text-star' : 'text-stone-300' }}" />
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-ink">{{ number_format($restaurant->rating, 1) }} / 5.0</span>
                            <span class="text-xs text-muted">({{ $restaurant->reviews_count }} değerlendirme)</span>
                        </div>
                    </div>
                    <button type="button" @click="showForm = !showForm"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        <x-ico name="chat" class="w-3.5 h-3.5" />
                        <span x-text="showForm ? 'Formu Gizle' : 'Yorum ve Puan Yaz'"></span>
                    </button>
                </div>

                <!-- Review Form -->
                <form id="review-form" x-show="showForm" x-cloak method="POST"
                      action="{{ $firstBranchId ? route('branches.reviews.store', $firstBranchId) : '#' }}"
                      class="p-6 rounded-3xl bg-surface border border-stone-200/70 shadow-xs space-y-4 transition-all">
                    @csrf
                    <div>
                        <span class="block text-xs font-bold text-ink mb-2">Deneyiminizi Puanlayın:</span>
                        <div class="flex items-center gap-2">
                            <template x-for="s in [1,2,3,4,5]" :key="s">
                                <button type="button" @click="rating = s" :aria-label="'Puan ' + s"
                                        :class="s <= rating ? 'text-star' : 'text-stone-300'" class="focus:outline-none transition-transform hover:scale-110 cursor-pointer">
                                    <x-ico name="star" filled class="w-6 h-6" />
                                </button>
                            </template>
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="review-author" class="block text-xs font-bold text-muted mb-1">Adınız / Rumuz</label>
                            <input id="review-author" type="text" name="author_name" placeholder="Misafir"
                                   class="w-full px-4 py-2.5 rounded-xl bg-sand text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta placeholder:text-muted/60 border-0">
                        </div>
                        <div>
                            <label for="review-branch" class="block text-xs font-bold text-muted mb-1">Şube</label>
                            @if($hasMultipleBranches)
                                <select id="review-branch"
                                        @change="document.getElementById('review-form').action = $event.target.selectedOptions[0].dataset.url"
                                        class="w-full px-4 py-2.5 rounded-xl bg-sand text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta border-0">
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
                                  placeholder="Lezzet, servis kalitesi ve atmosfer hakkında ne düşünüyorsunuz?"
                                  class="w-full px-4 py-2.5 rounded-xl bg-sand text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta placeholder:text-muted/60 resize-none border-0"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-xs font-bold text-muted hover:text-ink cursor-pointer">İptal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold shadow-xs cursor-pointer">Değerlendirmeyi Gönder</button>
                    </div>
                </form>

                <!-- Reviews Grid -->
                @if($allReviews->isEmpty())
                    <div class="p-8 rounded-2xl bg-surface/60 text-center">
                        <p class="text-xs text-muted italic">Bu mekan için henüz yorum bırakılmamış. İlk değerlendirmeyi siz yapın!</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($allReviews->take(6) as $rev)
                            <article class="p-5 rounded-2xl bg-surface border border-stone-200/50 flex flex-col justify-between shadow-2xs">
                                <div>
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-sand text-ink font-bold text-xs flex items-center justify-center">
                                                {{ mb_substr($rev->author_name ?: 'A', 0, 1) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-xs text-ink block">{{ $rev->author_name ?: 'Anonim misafir' }}</span>
                                                <span class="text-[10px] text-muted">{{ $rev->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-ico name="star" filled class="w-3 h-3 {{ $i <= $rev->rating ? 'text-star' : 'text-stone-200' }}" />
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rev->comment)
                                        <p class="mt-3 text-xs text-stone-700 leading-relaxed font-normal">{{ $rev->comment }}</p>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

        </div>

        <!-- Right Column: Sidebar (Working Hours, Map, Location) (4 Cols) -->
        <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

            <!-- Working Hours Card -->
            <div class="p-6 rounded-3xl bg-surface border border-stone-200/70 shadow-2xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                    <div class="flex items-center gap-2">
                        <x-ico name="clock" class="w-4 h-4 text-terracotta" />
                        <h3 class="text-sm font-bold text-ink">Çalışma Saatleri</h3>
                    </div>
                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full {{ $todayOpen ? 'bg-emerald-50 text-open' : 'bg-rose-50 text-rose-700' }}">
                        {{ $todayOpen ? 'ŞU AN AÇIK' : 'KAPALI' }}
                    </span>
                </div>

                <ul class="divide-y divide-stone-100 text-xs">
                    @foreach($days as $key => $name)
                        @php
                            $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                            $isToday = $key === $todayKey;
                            $closed = is_array($cfg) && !empty($cfg['is_closed']);
                            $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . ' – ' . $cfg['close'] : null;
                            $time = $closed ? 'Kapalı' : ($range ?? ($schedule->opening_hours ?? '10:00 – 23:00'));
                        @endphp
                        <li class="flex items-center justify-between py-2.5 {{ $isToday ? 'font-bold text-terracotta bg-orange-50/40 -mx-3 px-3 rounded-lg' : 'text-stone-600' }}">
                            <span class="flex items-center gap-1.5">
                                <span>{{ $name }}</span>
                                @if($isToday)
                                    <span class="text-[9px] uppercase px-1.5 py-0.2 rounded bg-terracotta text-white font-bold">Bugün</span>
                                @endif
                            </span>
                            <span class="{{ $closed ? 'italic text-stone-400' : 'font-mono text-ink' }}">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Location & Interactive Map Card -->
            <div class="p-6 rounded-3xl bg-surface border border-stone-200/70 shadow-2xs space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-stone-100">
                    <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
                    <h3 class="text-sm font-bold text-ink">Konum & Ulaşım</h3>
                </div>

                <div class="h-48 rounded-2xl overflow-hidden relative shadow-inner bg-stone-100"
                     x-data="{ init() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                         const m = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                         L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(m);
                         L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: L.divIcon({ className: 'custom-pin', html: '<div style=\'background:#E85D3F;color:#fff;padding:3px 7px;border-radius:9999px;font-weight:800;font-size:10px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.2);\'>★</div>', iconSize: [26,20], iconAnchor: [13,10] }) }).addTo(m);
                     }); } }" x-init="init()"></div>

                @if($address)
                    <p class="text-xs text-stone-600 leading-relaxed font-normal">{{ $address }}</p>
                @endif

                <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center justify-center w-full gap-2 px-4 py-2.5 rounded-xl bg-sand hover:bg-stone-200/70 text-ink font-bold text-xs transition-colors">
                    <x-ico name="external" class="w-3.5 h-3.5 text-terracotta" />
                    <span>Google Haritalar'da Aç</span>
                </a>
            </div>

        </aside>

    </div>

    <!-- ================= RELATED RESTAURANTS ================= -->
    @if($relatedRestaurants->isNotEmpty())
        <section class="mt-16 pt-12 border-t border-stone-200/70">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-ink font-display">{{ $restaurant->city->name }} Çevresindeki Mekanlar</h2>
                    <p class="text-xs text-muted mt-0.5">Yakındaki diğer popüler restoran ve kafeler</p>
                </div>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
                   class="inline-flex items-center gap-1 text-xs sm:text-sm font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                    <span>Tümünü gör</span>
                    <x-ico name="chevron-right" class="w-4 h-4" />
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

<!-- ================= MOBILE STICKY BOTTOM ACTION BAR ================= -->
<div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-stone-200/80 p-3 px-4 flex items-center gap-3 shadow-2xl">
    @if($restaurant->phone)
        <a href="tel:{{ $restaurant->phone }}"
           class="flex-1 inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-sand text-ink font-bold text-xs border border-stone-200/80">
            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
            <span>Ara</span>
        </a>
    @endif
    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
       class="flex-[2] inline-flex items-center justify-center gap-2 py-3 rounded-xl bg-terracotta text-white font-bold text-xs shadow-md">
        <x-ico name="book-open" class="w-4 h-4" />
        <span>Dijital Menü</span>
    </a>
</div>

@endsection
