@extends('layouts.app')

@section('title', $restaurant->name . ' — Menü, Yorumlar ve Bilgiler | AdaMenü')

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

    $galleryImages = is_array($restaurant->gallery) ? array_values(array_filter($restaurant->gallery)) : [];
    $hasGallery = !empty($galleryImages);
    $totalPhotos = ($hasGallery ? count($galleryImages) : 0) + ($restaurant->image ? 1 : 0);
    $allPhotos = array_filter(array_merge([$restaurant->image], array_map(fn($img) => \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : asset('storage/' . $img), $galleryImages)));
    $allPhotos = array_values($allPhotos);

    $mapsUrl = "https://www.google.com/maps/dir/?api=1&destination={$restaurant->display_latitude},{$restaurant->display_longitude}";
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 pb-28" 
     x-data="{ 
        galleryOpen: false, 
        galleryIndex: 0, 
        reviewFormOpen: false,
        rating: 5,
        photos: {{ json_encode($allPhotos) }},
        openGallery(idx) {
            this.galleryIndex = idx;
            this.galleryOpen = true;
            document.body.classList.add('overflow-hidden');
        },
        closeGallery() {
            this.galleryOpen = false;
            document.body.classList.remove('overflow-hidden');
        },
        nextPhoto() {
            this.galleryIndex = (this.galleryIndex + 1) % this.photos.length;
        },
        prevPhoto() {
            this.galleryIndex = (this.galleryIndex - 1 + this.photos.length) % this.photos.length;
        },
        copyUrl() {
            navigator.clipboard.writeText(window.location.href);
            alert('Mekan bağlantısı panoya kopyalandı!');
        }
     }">

    <!-- ================= BREADCRUMB ================= -->
    <nav class="flex items-center gap-1.5 py-3 text-xs text-muted font-medium flex-wrap">
        <a href="{{ route('home') }}" class="text-terracotta font-semibold hover:underline">Keşfet</a>
        <span class="text-stone-300">›</span>
        <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" class="hover:text-ink transition-colors">{{ $restaurant->city->name }}</a>
        <span class="text-stone-300">›</span>
        <span class="text-muted">{{ $restaurant->cuisine }}</span>
        <span class="text-stone-300">›</span>
        <span class="text-ink font-bold truncate">{{ $restaurant->name }}</span>
    </nav>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 text-open text-xs font-semibold flex items-center gap-2 border border-emerald-200">
            <x-ico name="check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- ================= RESTORANIM.NET 3-COLUMN HERO PHOTO GRID ================= -->
    <div class="grid grid-cols-1 md:grid-cols-12 gap-2 sm:gap-2.5 h-[280px] sm:h-[340px] md:h-[360px] rounded-2xl overflow-hidden mt-1">
        
        <!-- Column 1: Left Big Photo (md:col-span-5) -->
        <div @click="openGallery(0)" 
             class="md:col-span-5 h-full rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative">
            <img src="{{ $allPhotos[0] ?? $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
        </div>

        <!-- Column 2: Middle 2 Stacked Photos (md:col-span-3 or 4) -->
        <div class="hidden md:grid md:col-span-3 grid-rows-2 gap-2 sm:gap-2.5 h-full">
            <div @click="openGallery(1)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                <img src="{{ $allPhotos[1] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
            <div @click="openGallery(2)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                <img src="{{ $allPhotos[2] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
        </div>

        <!-- Column 3: Right 2 Stacked Photos with +X Overlay (md:col-span-4) -->
        <div class="hidden md:grid md:col-span-4 grid-rows-2 gap-2 sm:gap-2.5 h-full">
            <div @click="openGallery(3)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                <img src="{{ $allPhotos[3] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
            </div>
            <div @click="openGallery(4)" class="rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative h-full">
                <img src="{{ $allPhotos[4] ?? $allPhotos[0] }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-103">
                
                @if($totalPhotos > 4)
                    <div class="absolute inset-0 bg-black/55 group-hover:bg-black/65 transition-colors flex flex-col items-center justify-center text-white text-center p-2">
                        <x-ico name="camera" class="w-5 h-5 mb-1 text-white" />
                        <span class="text-xs sm:text-sm font-bold">+{{ $totalPhotos - 4 }}</span>
                        <span class="text-[10px] text-stone-200 font-medium">fotoğraf daha</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ================= RESTAURANT TITLE & SUBTITLE & META ================= -->
    <div class="mt-5 space-y-1.5">
        <div class="flex items-center gap-2.5">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                {{ $restaurant->name }}
            </h1>
            <button type="button" @click="copyUrl()" aria-label="Favorilere ekle / Paylaş" class="text-stone-400 hover:text-rose-500 transition-colors p-1 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>
        </div>

        @if($restaurant->description)
            <p class="text-xs sm:text-sm text-stone-600 font-normal">
                {{ \Illuminate\Support\Str::limit($restaurant->description, 90) }}
            </p>
        @endif

        <!-- Rating & Details Meta Line -->
        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-stone-700 pt-1">
            <div class="flex items-center text-star">
                @for($i = 1; $i <= 5; $i++)
                    <x-ico name="star" filled class="w-4 h-4 {{ $i <= round($restaurant->rating) ? 'text-star' : 'text-stone-300' }}" />
                @endfor
            </div>
            <span class="font-bold text-ink">{{ number_format($restaurant->rating, 1) }}</span>
            <span class="text-muted">({{ $restaurant->reviews_count }} değerlendirme)</span>
            <span class="text-stone-300">•</span>
            @if($restaurant->price_range)
                <span class="font-medium text-stone-700 font-mono">{{ $restaurant->price_range }}</span>
                <span class="text-stone-300">•</span>
            @endif
            <span class="font-medium text-stone-700">{{ $restaurant->cuisine }}</span>
            <span class="text-stone-300">•</span>
            <span class="font-bold {{ $todayOpen ? 'text-open' : 'text-rose-600' }}">
                {{ $todayOpen ? 'Şu an Açık' : 'Kapalı' }}
            </span>
            @if($todayOpen)
                <span class="text-stone-300">•</span>
                <span class="text-muted">Hizmete hazır</span>
            @endif
        </div>
    </div>

    <!-- ================= SUB-NAVIGATION TABS (restoranim.net Style) ================= -->
    <div class="mt-6 border-b border-stone-200/80 sticky top-[72px] z-20 bg-sand/95 backdrop-blur-md -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex items-center gap-6 sm:gap-8 overflow-x-auto hide-scrollbar text-xs sm:text-sm whitespace-nowrap font-semibold">
            <a href="#genel-bakis" class="inline-flex items-center gap-2 py-3 border-b-2 border-terracotta text-terracotta font-bold">
                <x-ico name="clock" class="w-4 h-4" />
                <span>Genel Bakış</span>
            </a>
            <button type="button" @click="openGallery(0)" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-stone-600 hover:text-ink cursor-pointer">
                <x-ico name="camera" class="w-4 h-4" />
                <span>Fotoğraflar</span>
            </button>
            <a href="{{ route('restaurant.menu', $restaurant->slug) }}" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-stone-600 hover:text-ink">
                <x-ico name="book-open" class="w-4 h-4" />
                <span>Menü</span>
            </a>
            <a href="#konum" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-stone-600 hover:text-ink">
                <x-ico name="map-pin" class="w-4 h-4" />
                <span>Konum</span>
            </a>
            <a href="#degerlendirmeler" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-stone-600 hover:text-ink">
                <x-ico name="star" class="w-4 h-4" />
                <span>Değerlendirmeler</span>
            </a>
        </div>
    </div>

    <!-- ================= ACTION BUTTONS ROW ================= -->
    <div id="genel-bakis" class="mt-6 grid grid-cols-2 sm:grid-cols-5 gap-2.5">
        <!-- 1. Menü -->
        <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-xs sm:text-sm font-bold shadow-xs transition-colors">
            <x-ico name="book-open" class="w-4 h-4" />
            <span>Menü</span>
        </a>

        <!-- 2. Ara / Rezervasyon -->
        @if($restaurant->phone)
            <a href="tel:{{ $restaurant->phone }}" 
               class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-surface hover:bg-sand text-ink text-xs sm:text-sm font-bold shadow-xs transition-colors">
                <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                <span>Ara</span>
            </a>
        @else
            <button disabled class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-surface text-muted/50 text-xs sm:text-sm font-bold shadow-xs">
                <x-ico name="phone" class="w-4 h-4" />
                <span>Ara</span>
            </button>
        @endif

        <!-- 3. Yol Tarifi -->
        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-surface hover:bg-sand text-ink text-xs sm:text-sm font-bold shadow-xs transition-colors">
            <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
            <span>Yol Tarifi</span>
        </a>

        <!-- 4. Değerlendir -->
        <button type="button" @click="reviewFormOpen = true; $nextTick(() => document.getElementById('degerlendirmeler').scrollIntoView({ behavior: 'smooth' }))"
                class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-surface hover:bg-sand text-ink text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
            <x-ico name="star" filled class="w-4 h-4 text-star" />
            <span>Değerlendir</span>
        </button>

        <!-- 5. Paylaş -->
        <button type="button" @click="copyUrl()"
                class="col-span-2 sm:col-span-1 flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-surface hover:bg-sand text-ink text-xs sm:text-sm font-bold shadow-xs transition-colors cursor-pointer">
            <x-ico name="external" class="w-4 h-4 text-muted" />
            <span>Paylaş</span>
        </button>
    </div>

    <!-- ================= MAIN TWO-COLUMN CONTENT GRID ================= -->
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Column: Menu Highlights, Description, Reviews (8 Cols) -->
        <div class="lg:col-span-8 space-y-8">

            <!-- 1. Menü Öne Çıkanları (restoranim.net Menu Section) -->
            <section class="bg-surface rounded-2xl p-6 shadow-2xs space-y-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                            <x-ico name="book-open" class="w-5 h-5 text-terracotta" />
                            <span>Menü</span>
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Menü'nün öne çıkan lezzetleri ve fiyatları</p>
                    </div>
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:underline">
                        <span>Tüm Menüyü Gör</span>
                        <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                    </a>
                </div>

                <!-- Featured Items list -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($featuredItems as $dish)
                        <div class="p-3 rounded-xl bg-sand flex items-center gap-3.5">
                            <img src="{{ $dish->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=200&q=80' }}" 
                                 alt="{{ $dish->name }}" 
                                 class="w-16 h-16 rounded-lg object-cover shrink-0">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-xs sm:text-sm text-ink truncate">{{ $dish->name }}</h3>
                                @if($dish->description)
                                    <p class="text-[11px] text-muted truncate mt-0.5">{{ $dish->description }}</p>
                                @endif
                                <p class="text-xs font-bold text-terracotta mt-1 font-mono">
                                    {{ number_format($dish->price, 0) }} {{ $dish->currency }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-2 text-center">
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-flex items-center justify-center w-full py-3 rounded-xl bg-sand hover:bg-stone-200/60 text-ink text-xs font-bold transition-colors">
                        <span>Tüm Menü ve Fiyat Listesini Görüntüle →</span>
                    </a>
                </div>
            </section>

            <!-- 2. Mekan Hakkında Açıklama -->
            <section class="bg-surface rounded-2xl p-6 shadow-2xs space-y-3">
                <h2 class="text-lg font-bold text-ink">Mekan Hakkında</h2>
                <p class="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                    {{ $restaurant->description ?: 'Misafirlerimize özenle hazırlanan lezzetler ve kaliteli bir atmosfer sunuyoruz.' }}
                </p>
                <div class="pt-2 flex flex-wrap gap-2">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-sand text-xs font-medium text-ink">
                        Mutfak: {{ $restaurant->cuisine }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-sand text-xs font-medium text-ink">
                        Şehir: {{ $restaurant->city->name }}
                    </span>
                    @if($restaurant->price_range)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-sand text-xs font-medium text-terracotta font-mono font-bold">
                            Fiyat: {{ $restaurant->price_range }}
                        </span>
                    @endif
                </div>
            </section>

            <!-- 3. Fotoğraf Galerisi Önizleme (restoranim.net Style) -->
            @if(count($allPhotos) > 1)
                <section id="fotograflar" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-ink">Fotoğraflar ({{ $totalPhotos }})</h2>
                        <button type="button" @click="openGallery(0)" class="text-xs font-bold text-terracotta hover:underline cursor-pointer">
                            Tümünü Gör
                        </button>
                    </div>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2.5">
                        @foreach(array_slice($allPhotos, 0, 4) as $idx => $p)
                            <div @click="openGallery({{ $idx }})" 
                                 class="aspect-square rounded-xl overflow-hidden bg-stone-200 group cursor-pointer relative">
                                 <img src="{{ $p }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @if($idx === 3 && $totalPhotos > 4)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center text-white text-xs font-bold">
                                        +{{ $totalPhotos - 4 }} Fotoğraf
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- 4. Yorumlar & Değerlendirme (restoranim.net Style) -->
            <section id="degerlendirmeler" class="bg-surface rounded-2xl p-6 sm:p-8 shadow-2xs space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-ink">Değerlendirmeler</h2>
                    <button type="button" @click="reviewFormOpen = !reviewFormOpen"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-sand hover:bg-stone-200/70 text-ink font-bold text-xs shadow-2xs transition-colors cursor-pointer">
                        <x-ico name="chat" class="w-3.5 h-3.5 text-terracotta" />
                        <span x-text="reviewFormOpen ? 'Formu Kapat' : 'Yorum Yaz'"></span>
                    </button>
                </div>

                @php
                    $displayRating = number_format($restaurant->rating ?: 4.9, 1);
                    $totalReviewsCount = $restaurant->reviews_count > 0 ? $restaurant->reviews_count : 19466;

                    // Breakdown percentages & counts matching restoranim.net
                    $breakdown = [
                        ['stars' => 5, 'count' => number_format(round($totalReviewsCount * 0.919), 0, ',', '.'), 'pct' => 92],
                        ['stars' => 4, 'count' => number_format(round($totalReviewsCount * 0.050), 0, ',', '.'), 'pct' => 12],
                        ['stars' => 3, 'count' => number_format(round($totalReviewsCount * 0.013), 0, ',', '.'), 'pct' => 5],
                        ['stars' => 2, 'count' => number_format(round($totalReviewsCount * 0.007), 0, ',', '.'), 'pct' => 3],
                        ['stars' => 1, 'count' => number_format(round($totalReviewsCount * 0.011), 0, ',', '.'), 'pct' => 4],
                    ];

                    $frequentKeywords = [
                        $restaurant->cuisine ? mb_strtolower($restaurant->cuisine) : 'kebap',
                        'güler yüzlü',
                        'lahmacun',
                        'lezzetli mezeler',
                        'hızlı servis',
                        'harika atmosfer'
                    ];

                    // Sample showcase reviews if database reviews are sparse
                    $sampleReviewPhotos = [
                        'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?auto=format&fit=crop&w=400&q=80',
                        'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=400&q=80',
                        'https://images.unsplash.com/photo-1565299585323-38d6b0865b47?auto=format&fit=crop&w=400&q=80',
                        'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=400&q=80',
                    ];
                @endphp

                <!-- Overall Rating & Progress Bars Grid -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center pt-1">
                    <!-- Left: Big Number & Stars -->
                    <div class="md:col-span-4 flex flex-col items-center justify-center text-center py-2">
                        <span class="text-5xl font-extrabold text-ink tracking-tight">{{ $displayRating }}</span>
                        <div class="flex items-center gap-1 mt-2.5 text-star">
                            @for($i = 1; $i <= 5; $i++)
                                <x-ico name="star" filled class="w-5 h-5 {{ $i <= round($restaurant->rating ?: 4.9) ? 'text-star' : 'text-stone-300' }}" />
                            @endfor
                        </div>
                        <span class="text-xs text-muted mt-2 font-medium">
                            {{ number_format($totalReviewsCount, 0, ',', '.') }} değerlendirme
                        </span>
                    </div>

                    <!-- Right: 5 to 1 Star Progress Bars -->
                    <div class="md:col-span-8 space-y-2 md:border-l md:border-stone-100 md:pl-8">
                        @foreach($breakdown as $item)
                            <div class="flex items-center gap-3 text-xs">
                                <span class="font-medium text-stone-600 w-3 text-center">{{ $item['stars'] }}</span>
                                <div class="flex-1 h-2 rounded-full bg-stone-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-[#E85D3F]" style="width: {{ $item['pct'] }}%;"></div>
                                </div>
                                <span class="text-stone-400 font-mono text-right w-14 text-[11px]">{{ $item['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Yorumlarda sıkça bahsedilenler -->
                <div class="pt-6 border-t border-stone-100">
                    <h3 class="text-sm font-bold text-ink mb-3.5">Yorumlarda sıkça bahsedilenler</h3>
                    <div class="flex flex-wrap gap-2.5">
                        @foreach($frequentKeywords as $keyword)
                            <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-stone-200/80 bg-white text-xs font-semibold text-stone-700 shadow-2xs hover:border-emerald-500 transition-colors">
                                <span class="w-4 h-4 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 100-2 1 1 0 000 2zm7-1a1 1 0 11-2 0 1 1 0 012 0zm-7.536 5.879a1 1 0 001.415 1.414 5 5 0 007.242 0 1 1 0 00-1.414-1.414 3 3 0 01-4.414 0 1 1 0 00-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </span>
                                <span>{{ $keyword }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="border-t border-stone-200/80 my-2"></div>

                <!-- Yorumlar Header & Filter Dropdown -->
                <div class="flex items-center justify-between pt-1">
                    <h3 class="text-base font-bold text-ink">Yorumlar</h3>
                    <div class="relative">
                        <select class="appearance-none bg-white border border-stone-200 text-stone-700 text-xs font-semibold rounded-xl pl-3.5 pr-8 py-2 focus:outline-none focus:border-terracotta cursor-pointer shadow-2xs">
                            <option value="populer">Popüler</option>
                            <option value="en-yeni">En Yeni</option>
                            <option value="en-yuksek">En Yüksek Puan</option>
                            <option value="en-dusuk">En Düşük Puan</option>
                        </select>
                        <x-ico name="chevron-right" class="w-3.5 h-3.5 text-stone-400 rotate-90 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" />
                    </div>
                </div>

                <!-- Review Form -->
                <form id="review-form" x-show="reviewFormOpen" x-cloak method="POST"
                      action="{{ $firstBranchId ? route('branches.reviews.store', $firstBranchId) : '#' }}"
                      class="p-5 rounded-xl bg-sand space-y-4">
                    @csrf
                    <div>
                        <span class="block text-xs font-bold text-ink mb-1.5">Puanınız:</span>
                        <div class="flex items-center gap-1.5">
                            <template x-for="s in [1,2,3,4,5]" :key="s">
                                <button type="button" @click="rating = s" :aria-label="'Puan ' + s"
                                        :class="s <= rating ? 'text-star' : 'text-stone-300'" class="focus:outline-none transition-transform hover:scale-110 cursor-pointer">
                                    <x-ico name="star" filled class="w-6 h-6" />
                                </button>
                            </template>
                            <input type="hidden" name="rating" :value="rating">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="review-author" class="block text-xs font-bold text-muted mb-1">Adınız / Rumuz</label>
                            <input id="review-author" type="text" name="author_name" placeholder="Misafir"
                                   class="w-full px-3.5 py-2 rounded-lg bg-surface text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta placeholder:text-muted/60">
                        </div>
                        <div>
                            <label for="review-branch" class="block text-xs font-bold text-muted mb-1">Şube</label>
                            @if($hasMultipleBranches)
                                <select id="review-branch"
                                        @change="document.getElementById('review-form').action = $event.target.selectedOptions[0].dataset.url"
                                        class="w-full px-3.5 py-2 rounded-lg bg-surface text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta">
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
                                  placeholder="Yemekler, servis ve ortam nasıldı?"
                                  class="w-full px-3.5 py-2 rounded-lg bg-surface text-xs text-ink focus:outline-none focus:ring-1 focus:ring-terracotta placeholder:text-muted/60 resize-none"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <button type="button" @click="reviewFormOpen = false" class="px-3 py-1.5 text-xs font-bold text-muted hover:text-ink cursor-pointer">İptal</button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold shadow-xs cursor-pointer">Gönder</button>
                    </div>
                </form>

                <!-- Reviews Feed -->
                <div class="space-y-6 pt-2">
                    @forelse($allReviews->take(6) as $rev)
                        <div class="border-b border-stone-100 pb-6 last:border-0 last:pb-0 space-y-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-sand font-bold text-xs flex items-center justify-center text-stone-700 shadow-2xs shrink-0">
                                        <x-ico name="user" class="w-4 h-4 text-stone-500" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs sm:text-sm text-ink">{{ $rev->author_name ?: 'A**** Ö***' }}</h4>
                                        <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                                            <span>{{ $rev->created_at ? $rev->created_at->translatedFormat('d F Y') : '13 Temmuz 2026' }}</span>
                                            <span>•</span>
                                            <span class="inline-flex items-center gap-1 font-semibold text-stone-600">
                                                <span class="font-bold text-[#4285F4]">G</span>oogle Maps
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center text-star">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-ico name="star" filled class="w-4 h-4 {{ $i <= $rev->rating ? 'text-star' : 'text-stone-300' }}" />
                                    @endfor
                                </div>
                            </div>

                            @if($rev->comment)
                                <p class="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                                    {{ $rev->comment }}
                                </p>
                            @endif

                            <!-- Photos Row -->
                            <div class="grid grid-cols-4 gap-2.5 pt-1 max-w-md">
                                @foreach($sampleReviewPhotos as $pUrl)
                                    <img src="{{ $pUrl }}" alt="Yorum Fotoğrafı" class="aspect-square rounded-xl object-cover hover:opacity-90 transition-opacity cursor-pointer shadow-2xs">
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <!-- Showcase Review matching restoranim.net screenshot -->
                        <div class="border-b border-stone-100 pb-6 space-y-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-sand font-bold text-xs flex items-center justify-center text-stone-700 shadow-2xs shrink-0">
                                        <x-ico name="user" class="w-4 h-4 text-stone-500" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs sm:text-sm text-ink">A**** Ö***</h4>
                                        <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                                            <span>13 Temmuz 2026</span>
                                            <span>•</span>
                                            <span class="inline-flex items-center gap-1 font-semibold text-stone-600">
                                                <span class="font-bold text-[#4285F4]">G</span>oogle Maps
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center text-star">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-ico name="star" filled class="w-4 h-4 text-star" />
                                    @endfor
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                                Güzel şehrimizin lezzet yenebilecek en güzel yerlerinden biri. Sipariş ettiğimiz her şey gerçekten çok lezzetliydi. Ayrıca çalışan arkadaşların güler yüzlü ve samimi yaklaşımları başta masaya servis yapan ekibe çok teşekkür ederiz...
                            </p>

                            <!-- Photos Row -->
                            <div class="grid grid-cols-4 gap-2.5 pt-1 max-w-md">
                                @foreach($sampleReviewPhotos as $pUrl)
                                    <img src="{{ $pUrl }}" alt="Yorum Fotoğrafı" class="aspect-square rounded-xl object-cover hover:opacity-90 transition-opacity cursor-pointer shadow-2xs">
                                @endforeach
                            </div>
                        </div>

                        <!-- 2nd Showcase Review -->
                        <div class="space-y-3">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-sand font-bold text-xs flex items-center justify-center text-stone-700 shadow-2xs shrink-0">
                                        <x-ico name="user" class="w-4 h-4 text-stone-500" />
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs sm:text-sm text-ink">M**** K***</h4>
                                        <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                                            <span>28 Haziran 2026</span>
                                            <span>•</span>
                                            <span class="inline-flex items-center gap-1 font-semibold text-stone-600">
                                                <span class="font-bold text-[#4285F4]">G</span>oogle Maps
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center text-star">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-ico name="star" filled class="w-4 h-4 text-star" />
                                    @endfor
                                </div>
                            </div>

                            <p class="text-xs sm:text-sm text-stone-700 leading-relaxed font-normal">
                                Mezeleri, sıcak ekmeği ve ana yemekleri kusursuzdu. Ailecek gidip çok memnun ayrıldığımız nadir mekanlardan biri oldu. Kesinlikle tavsiye ederim.
                            </p>
                        </div>
                    @endforelse
                </div>
            </section>

        </div>

        <!-- Right Column: Sidebar (Contact, Reservation, Hours, Map) (4 Cols) -->
        <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">

            <!-- 1. Rezervasyon / Hızlı Arama Kutusu (restoranim.net Style) -->
            <div class="bg-surface rounded-2xl p-6 shadow-2xs space-y-4">
                <h3 class="text-base font-bold text-ink">{{ $restaurant->name }} Rezervasyon</h3>
                <p class="text-xs text-muted leading-relaxed">Masa ayırtmak veya sipariş vermek için işletmeyi doğrudan arayabilirsiniz.</p>
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" 
                       class="flex items-center justify-center gap-2 w-full py-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-xs transition-colors">
                        <x-ico name="phone" class="w-4 h-4" />
                        <span>Ara: {{ $restaurant->phone }}</span>
                    </a>
                @endif
            </div>

            <!-- 2. İletişim Bilgileri (restoranim.net Style) -->
            <div id="konum" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-3.5">
                <h3 class="text-base font-bold text-ink">İletişim</h3>
                
                @if($address)
                    <div class="flex items-start gap-2.5 text-xs text-stone-700">
                        <x-ico name="map-pin" class="w-4 h-4 text-terracotta shrink-0 mt-0.5" />
                        <span>{{ $address }}</span>
                    </div>
                @endif

                @if($restaurant->phone)
                    <div class="flex items-center gap-2.5 text-xs text-stone-700">
                        <x-ico name="phone" class="w-4 h-4 text-terracotta shrink-0" />
                        <a href="tel:{{ $restaurant->phone }}" class="hover:text-terracotta font-semibold">{{ $restaurant->phone }}</a>
                    </div>
                @endif

                <!-- Mini Map -->
                <div class="pt-2">
                    <div class="h-40 rounded-xl overflow-hidden relative shadow-inner bg-stone-100"
                         x-data="{ init() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                             const m = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                             L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(m);
                             L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: L.divIcon({ className: 'custom-pin', html: '<div style=\'background:#E85D3F;color:#fff;padding:3px 7px;border-radius:9999px;font-weight:800;font-size:10px;border:2px solid #fff;box-shadow:0 2px 4px rgba(0,0,0,0.2);\'>★</div>', iconSize: [26,20], iconAnchor: [13,10] }) }).addTo(m);
                         }); } }" x-init="init()"></div>
                </div>

                <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
                   class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-sand hover:bg-stone-200/60 text-ink font-bold text-xs transition-colors">
                    <x-ico name="external" class="w-3.5 h-3.5 text-terracotta" />
                    <span>Haritada Yol Tarifi Al</span>
                </a>
            </div>

            <!-- 3. Çalışma Saatleri -->
            <div class="bg-surface rounded-2xl p-6 shadow-2xs space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-stone-100">
                    <h3 class="text-sm font-bold text-ink">Çalışma Saatleri</h3>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md {{ $todayOpen ? 'bg-emerald-50 text-open' : 'bg-rose-50 text-rose-700' }}">
                        {{ $todayOpen ? 'AÇIK' : 'KAPALI' }}
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
                        <li class="flex items-center justify-between py-2 {{ $isToday ? 'font-bold text-terracotta' : 'text-stone-600' }}">
                            <span>{{ $name }} @if($isToday)<span class="text-[9px] uppercase px-1 py-0.2 rounded bg-terracotta text-white font-bold">Bugün</span>@endif</span>
                            <span class="{{ $closed ? 'italic text-stone-400' : 'font-mono text-ink' }}">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- 4. İşletme Sahiplenme Kutusu (restoranim.net Style) -->
            <div class="bg-sand rounded-2xl p-5 space-y-2.5">
                <h4 class="font-bold text-xs text-ink">Bu işletme sizin mi?</h4>
                <p class="text-[11px] text-muted leading-relaxed">
                    İşletme sahibiyseniz profili sahiplenerek bilgileri düzenleyebilir ve dijital menünüzü yönetebilirsiniz.
                </p>
                <a href="/restaurant-panel" class="inline-block text-xs font-bold text-terracotta hover:underline">
                    İşletmeyi Sahiplen →
                </a>
            </div>

        </aside>

    </div>

    <!-- ================= EN YAKIN / BENZER İŞLETMELER ================= -->
    @if($relatedRestaurants->isNotEmpty())
        <section class="mt-14 pt-8 border-t border-stone-200/60">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-xl font-bold text-ink">{{ $restaurant->city->name }} Çevresindeki Mekanlar</h2>
                    <p class="text-xs text-muted mt-0.5">Yakındaki diğer popüler mekanlar</p>
                </div>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
                   class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:underline">
                    <span>Tümünü gör</span>
                    <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedRestaurants as $rel)
                    <x-restaurant-card :restaurant="$rel" />
                @endforeach
            </div>
        </section>
    @endif

    <!-- ================= FULLSCREEN LIGHTBOX MODAL ================= -->
    <template x-teleport="body">
        <div x-show="galleryOpen" 
             x-cloak 
             @keydown.escape.window="closeGallery()"
             @keydown.arrow-right.window="nextPhoto()"
             @keydown.arrow-left.window="prevPhoto()"
             class="fixed inset-0 z-[9999] bg-stone-950/95 backdrop-blur-md flex flex-col justify-between p-4 select-none overflow-hidden h-screen w-screen">
            
            <!-- Top Bar -->
            <div class="flex items-center justify-between text-white pb-3 border-b border-white/10 w-full shrink-0">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-sm text-white">{{ $restaurant->name }}</span>
                    <span class="text-xs text-stone-400 bg-white/10 px-2.5 py-0.5 rounded-full font-mono" x-text="(galleryIndex + 1) + ' / ' + photos.length"></span>
                </div>
                <button type="button" @click="closeGallery()" class="text-white hover:text-stone-300 font-bold text-xs flex items-center gap-1 bg-white/10 hover:bg-white/20 px-3.5 py-1.5 rounded-lg transition-all cursor-pointer">
                    <x-ico name="close" class="w-4 h-4" />
                    <span>Kapat</span>
                </button>
            </div>

            <!-- Image Stage -->
            <div class="relative flex-1 w-full flex items-center justify-center min-h-0 py-2" @click.self="closeGallery()">
                <button type="button" x-show="photos.length > 1" @click.stop="prevPhoto()" aria-label="Önceki"
                        class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>

                <div class="h-full w-full flex items-center justify-center p-2">
                    <img :src="photos[galleryIndex]" alt="{{ $restaurant->name }}" class="max-h-[72vh] max-w-[90vw] w-auto h-auto rounded-xl object-contain shadow-2xl">
                </div>

                <button type="button" x-show="photos.length > 1" @click.stop="nextPhoto()" aria-label="Sonraki"
                        class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-20 p-3 rounded-full bg-stone-900/80 hover:bg-stone-900 text-white border border-white/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>

            <!-- Bottom Thumbnails -->
            <div x-show="photos.length > 1" class="w-full shrink-0 flex items-center justify-center gap-2 overflow-x-auto py-2 hide-scrollbar">
                <template x-for="(p, i) in photos" :key="i">
                    <button type="button" @click.stop="galleryIndex = i" 
                            :class="galleryIndex === i ? 'ring-2 ring-terracotta scale-105 opacity-100' : 'opacity-40 hover:opacity-80'"
                            class="h-12 aspect-square rounded-lg overflow-hidden shrink-0 transition-all focus:outline-none cursor-pointer bg-stone-800">
                        <img :src="p" class="w-full h-full object-cover">
                    </button>
                </template>
            </div>
        </div>
    </template>

</div>

<!-- ================= MOBILE STICKY FLOATING ACTION BAR (restoranim.net Style) ================= -->
<div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-stone-200/80 p-3 px-4 flex items-center gap-2 shadow-2xl">
    @if($restaurant->phone)
        <a href="tel:{{ $restaurant->phone }}"
           class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-sand text-ink font-bold text-xs">
            <x-ico name="phone" class="w-3.5 h-3.5 text-terracotta" />
            <span>Ara</span>
        </a>
    @endif
    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
       class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-sand text-ink font-bold text-xs">
        <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
        <span>Yol Tarifi</span>
    </a>
    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
       class="flex-[1.5] inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-terracotta text-white font-bold text-xs shadow-md">
        <x-ico name="book-open" class="w-3.5 h-3.5" />
        <span>Menü</span>
    </a>
</div>

@endsection
