@extends('layouts.app')

@section('title', $restaurant->name . ' — Menü, Konum ve Çalışma Saatleri | AdaMenü Kıbrıs')

@section('content')

@php
    $primary = $restaurant->branches->firstWhere('is_main', true) ?? $restaurant->branches->first();
    $hasMultipleBranches = $restaurant->branches->count() > 1;
    $hasSecondImage = !empty($restaurant->cover_image) && $restaurant->cover_image !== $restaurant->image;
    $todayOpen = $restaurant->isOpenNow();
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">

    <!-- Back control (single, not a breadcrumb trail) -->
    <a href="{{ route('restaurants.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-muted hover:text-ink">
        <x-ico name="chevron-right" class="w-3.5 h-3.5 rotate-180" />
        <span>Restoranlara Dön</span>
    </a>

    @if(session('success'))
        <div role="status" class="mt-5 flex items-center gap-3 px-4 py-3 rounded-2xl bg-open/10 border border-open/25 text-open">
            <x-ico name="check" class="w-5 h-5 shrink-0" />
            <span class="text-sm font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- ================= HERO IMAGE ================= -->
    <div class="mt-5 rounded-3xl overflow-hidden border border-warm bg-surface shadow-sm">
        <div class="relative aspect-[16/9] sm:aspect-[21/9] lg:aspect-[24/9] w-full bg-sand">
            <img src="{{ $restaurant->image }}"
                 alt="{{ $restaurant->name }} mekan görseli"
                 class="w-full h-full object-cover">
        </div>
        @if($hasSecondImage)
            <img src="{{ $restaurant->cover_image }}"
                 alt="{{ $restaurant->name }} ortam görseli"
                 class="w-full h-40 sm:h-56 object-cover border-t border-warm">
        @endif
    </div>

    <!-- ================= IDENTITY + ACTIONS ================= -->
    <section class="mt-6 bg-surface rounded-3xl border border-warm shadow-sm">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 sm:p-8 items-start">

            <!-- Identity -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sand border border-warm font-bold text-ink">
                        <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
                        {{ $restaurant->city->name }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sand border border-warm font-bold text-muted">
                        <x-ico name="tag" class="w-3.5 h-3.5" />
                        {{ $restaurant->cuisine }}
                    </span>
                    <span class="px-3 py-1 rounded-full bg-sand border border-warm font-bold font-mono text-ink">{{ $restaurant->price_range ?? '₺₺' }}</span>
                </div>

                <div>
                    <h1 class="text-3xl sm:text-4xl font-black text-ink tracking-tight">{{ $restaurant->name }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                        <span class="inline-flex items-center gap-1.5 font-bold text-ink">
                            <x-ico name="star" filled class="w-4 h-4 text-star" />
                            <span>{{ number_format($restaurant->rating, 1) }}</span>
                            <span class="text-muted font-semibold">({{ $restaurant->reviews_count }} değerlendirme)</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5 {{ $todayOpen ? 'text-open' : 'text-muted' }}">
                            <span class="w-2 h-2 rounded-full {{ $todayOpen ? 'bg-open' : 'bg-muted/50' }}"></span>
                            <span class="font-bold">{{ $todayOpen ? 'Şu anda açık' : 'Şu anda kapalı' }}</span>
                        </span>
                        <span class="text-muted">Bugün · {{ $restaurant->getTodayHours() }}</span>
                    </div>
                </div>

                @if($primary && $primary->address)
                    <p class="flex items-start gap-2 text-sm text-muted">
                        <x-ico name="map-pin" class="w-4 h-4 text-terracotta shrink-0 mt-0.5" />
                        <span>{{ $primary->address }}{{ $restaurant->city->name ? ', ' . $restaurant->city->name : '' }}</span>
                    </p>
                @endif
            </div>

            <!-- Primary actions -->
            <div class="lg:col-span-1 space-y-2.5">
                <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                   class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-xs">
                    <x-ico name="book-open" class="w-5 h-5" />
                    <span>Dijital Menü ve Fiyatlar</span>
                </a>
                <div class="grid grid-cols-2 gap-2.5">
                    @if($restaurant->phone)
                        <a href="tel:{{ $restaurant->phone }}"
                           class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs">
                            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                            <span>Ara</span>
                        </a>
                    @endif
                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                       target="_blank" rel="noopener" aria-label="{{ $restaurant->name }} yol tarifi"
                       class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs shadow-2xs">
                        <x-ico name="map" class="w-4 h-4 text-terracotta" />
                        <span>Yol Tarifi</span>
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ================= MAIN COLUMNS ================= -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-10 items-start">

        <!-- LEFT: narrative content -->
        <div class="lg:col-span-2 space-y-10">

            <!-- About -->
            <section>
                <h2 class="text-lg sm:text-xl font-extrabold text-ink">Mekan Hakkında</h2>
                <p class="mt-3 text-sm sm:text-base text-muted leading-relaxed">{{ $restaurant->description }}</p>
            </section>

            <!-- Featured dishes -->
            @if($featuredItems->isNotEmpty())
                <section class="pt-4">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="text-lg sm:text-xl font-extrabold text-ink">Öne Çıkan Lezzetler</h2>
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                           class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                            <span>Tüm menü</span>
                            <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                        </a>
                    </div>
                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($featuredItems as $dish)
                            <x-menu-item-card :dish="$dish" :showMenuLink="false" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Amenities -->
            @php
                $amenities = [
                    'Teras ve açık hava oturumu',
                    'Ücretsiz Wi-Fi',
                    'Kredi kartı ve temassız ödeme',
                    'Paket servis ve gel-al',
                    'Aile ve grup bölümü',
                    'Engelsiz giriş',
                ];
            @endphp
            @if(!empty($amenities))
                <section class="pt-4">
                    <h2 class="text-lg sm:text-xl font-extrabold text-ink">Öne Çıkan Özellikler</h2>
                    <ul class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2.5">
                        @foreach($amenities as $item)
                            <li class="flex items-center gap-2 text-sm text-ink">
                                <span class="w-5 h-5 rounded-full bg-open/10 flex items-center justify-center shrink-0">
                                    <x-ico name="check" class="w-3 h-3 text-open" />
                                </span>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            <!-- Branches (only when meaningful) -->
            @if($hasMultipleBranches)
                <section class="pt-4">
                    <h2 class="text-lg sm:text-xl font-extrabold text-ink">Şubeler</h2>
                    <div class="mt-4 space-y-3">
                        @foreach($restaurant->branches as $branch)
                            <div class="bg-surface rounded-2xl border border-warm p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-ink">{{ $branch->name }}</span>
                                        @if($branch->is_main)
                                            <span class="px-1.5 py-0.5 rounded bg-terracotta/10 text-terracotta text-[10px] font-extrabold">Merkez</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-muted mt-0.5 truncate">{{ $branch->address }}</p>
                                    <p class="text-xs text-muted mt-0.5">{{ $branch->getTodayHours() }}</p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($branch->phone)
                                        <a href="tel:{{ $branch->phone }}" class="text-xs font-bold text-ink hover:text-terracotta">Telefon</a>
                                    @endif
                                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}?branch={{ $branch->id }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold">
                                        Menü
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        <!-- RIGHT: practical sidebar (sticky) -->
        <aside class="lg:col-span-1 space-y-6 lg:sticky lg:top-24">

            <!-- Operating hours -->
            <section class="bg-surface rounded-2xl border border-warm shadow-2xs p-5">
                <div class="flex items-center justify-between pb-3 border-b border-warm">
                    <h2 class="inline-flex items-center gap-2 font-extrabold text-ink text-sm">
                        <x-ico name="clock" class="w-4 h-4 text-terracotta" />
                        Çalışma Saatleri
                    </h2>
                    <span class="w-2.5 h-2.5 rounded-full {{ $todayOpen ? 'bg-open' : 'bg-muted/50' }}"></span>
                </div>

                @php
                    $schedule = $primary;
                    $weekly = is_array($schedule->weekly_hours ?? null) ? $schedule->weekly_hours : ($restaurant->weekly_hours ?? null);
                    $days = [
                        'monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba',
                        'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar',
                    ];
                    $todayKey = strtolower(now()->format('l'));
                @endphp

                <ul class="mt-3 space-y-1 text-xs sm:text-sm">
                    @foreach($days as $key => $name)
                        @php
                            $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                            $isToday = $key === $todayKey;
                            $closed = is_array($cfg) && !empty($cfg['is_closed']);
                            $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . ' – ' . $cfg['close'] : null;
                            $time = $closed ? 'Kapalı' : ($range ?? ($schedule->opening_hours ?? '10:00 – 23:00'));
                        @endphp
                        <li class="flex items-center justify-between py-1.5 {{ $isToday ? 'px-2 rounded-lg bg-sand font-bold text-ink' : 'text-muted' }}">
                            <span class="flex items-center gap-1.5">
                                <span>{{ $name }}</span>
                                @if($isToday)
                                    <span class="px-1.5 py-0.5 rounded bg-terracotta text-white text-[10px] font-bold">Bugün</span>
                                @endif
                            </span>
                            <span class="{{ $closed ? 'text-muted' : 'font-mono text-ink' }}">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <!-- Location + mini map -->
            <section class="bg-surface rounded-2xl border border-warm shadow-2xs p-5 space-y-4">
                <h2 class="inline-flex items-center gap-2 font-extrabold text-ink text-sm">
                    <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
                    Konum ve Yol Tarifi
                </h2>

                <div class="h-44 rounded-xl overflow-hidden border border-warm"
                     x-data="{ initMap() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                        const map = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
                        const pin = L.divIcon({ className: 'custom-pin', html: `<div style='background:#E85D3F;color:#fff;padding:4px 8px;border-radius:9999px;font-weight:800;font-size:10px;border:2px solid #fff;white-space:nowrap;'>★</div>`, iconSize: [24, 24], iconAnchor: [12, 12] });
                        L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: pin }).addTo(map);
                     }); } }" x-init="initMap()"></div>

                <p class="text-xs text-muted">{{ $primary->address ?? $restaurant->address }}</p>

                <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                   target="_blank" rel="noopener"
                   class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sand hover:bg-surface border border-warm text-ink font-bold text-xs shadow-2xs">
                    <x-ico name="map" class="w-4 h-4 text-terracotta" />
                    <span>Google Haritalar'da aç</span>
                </a>
            </section>

            <!-- Contact -->
            @if($restaurant->phone)
                <section class="bg-surface rounded-2xl border border-warm shadow-2xs p-5 space-y-3">
                    <h2 class="inline-flex items-center gap-2 font-extrabold text-ink text-sm">
                        <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                        Telefon ve Rezervasyon
                    </h2>
                    <a href="tel:{{ $restaurant->phone }}" class="block text-sm font-bold text-ink hover:text-terracotta">{{ $restaurant->phone }}</a>
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                       class="block w-full text-center px-4 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs">
                        Menüyü telefonunuzda görüntüleyin
                    </a>
                </section>
            @endif

        </aside>
    </div>

    <!-- ================= REVIEWS ================= -->
    <section class="mt-8 bg-surface rounded-3xl border border-warm shadow-sm"
             x-data="{ activeBranchId: '{{ $primary->id ?? '' }}', selectedRating: 5, showForm: false }">

        <div class="p-6 sm:p-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sand text-terracotta flex items-center justify-center">
                        <x-ico name="chat" class="w-5 h-5" />
                    </div>
                    <div>
                        <h2 class="text-lg sm:text-xl font-extrabold text-ink">Misafir Yorumları</h2>
                        <p class="text-xs text-muted mt-0.5">Deneyiminizi paylaşın, gelecek misafirlere ışık tutun.</p>
                    </div>
                </div>
                <button type="button"
                        @click="showForm = !showForm"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shrink-0">
                    <span x-text="showForm ? 'Formu Kapat' : 'Puan ve Yorum Bırak'"></span>
                </button>
            </div>

            <!-- Branch switcher (only when several) -->
            @if($hasMultipleBranches)
                <div class="mt-4 flex flex-wrap items-center gap-2">
                    @foreach($restaurant->branches as $b)
                        <button type="button"
                                @click="activeBranchId = '{{ $b->id }}'"
                                :class="activeBranchId === '{{ $b->id }}' ? 'bg-ink text-white' : 'bg-sand text-muted border border-warm'"
                                class="px-3.5 py-1.5 rounded-full text-xs font-bold">
                            {{ $b->name }}
                        </button>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <div x-show="showForm" x-cloak class="mt-5 p-5 rounded-2xl bg-sand border border-warm space-y-4">
                <h3 class="text-sm font-bold text-ink">Deneyiminizi değerlendirin</h3>
                @foreach($restaurant->branches as $b)
                    <form x-show="activeBranchId == '{{ $b->id }}'"
                          action="{{ route('branches.reviews.store', $b->id) }}"
                          method="POST" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="rating-{{ $b->id }}" class="block text-xs font-bold text-muted mb-2">Puanınız (1–5)</label>
                                <div class="flex items-center gap-1">
                                    <template x-for="star in [1,2,3,4,5]" :key="star">
                                        <button type="button"
                                                @click="selectedRating = star"
                                                :aria-label="'Puan ' + star"
                                                :class="star <= selectedRating ? 'text-star' : 'text-muted/30'"
                                                class="focus:outline-none">
                                            <x-ico name="star" filled class="w-6 h-6" />
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" :value="selectedRating">
                            </div>
                            <div>
                                <label for="author-{{ $b->id }}" class="block text-xs font-bold text-muted mb-2">Adınız / Rumuz (isteğe bağlı)</label>
                                <input id="author-{{ $b->id }}" type="text" name="author_name"
                                       placeholder="Anonim Misafir"
                                       class="w-full px-4 py-2.5 bg-surface border border-warm rounded-xl text-sm text-ink focus:outline-none focus:border-terracotta font-medium">
                            </div>
                        </div>
                        <div>
                            <label for="comment-{{ $b->id }}" class="block text-xs font-bold text-muted mb-2">Yorumunuz</label>
                            <textarea id="comment-{{ $b->id }}" name="comment" rows="3"
                                      placeholder="Lezzet, servis ve ortam nasıldı?"
                                      class="w-full px-4 py-2.5 bg-surface border border-warm rounded-xl text-sm text-ink focus:outline-none focus:border-terracotta font-medium"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-xs font-bold text-muted hover:text-ink">İptal</button>
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-terracotta text-white font-bold text-xs">Yorumu Gönder</button>
                        </div>
                    </form>
                @endforeach
            </div>

            <!-- List -->
            @foreach($restaurant->branches as $b)
                <div x-show="activeBranchId == '{{ $b->id }}'" x-cloak class="mt-6">
                    @if($b->reviews->isEmpty())
                        <div class="p-6 text-center rounded-2xl border border-dashed border-warm text-muted text-sm">
                            Henüz yorum yapılmamış. İlk deneyimi siz paylaşın.
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($b->reviews as $rev)
                                <article class="p-4 rounded-2xl bg-sand border border-warm space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-sm text-ink">{{ $rev->author_name ?: 'Anonim Misafir' }}</span>
                                        <span class="inline-flex items-center gap-0.5">
                                            @for($i = 1; $i <= 5; $i++)
                                                <x-ico name="star" filled :class="'w-3.5 h-3.5 ' . ($i <= $rev->rating ? 'text-star' : 'text-muted/25')" />
                                            @endfor
                                        </span>
                                    </div>
                                    @if($rev->comment)
                                        <p class="text-xs text-ink/80 leading-relaxed">{{ $rev->comment }}</p>
                                    @endif
                                    <span class="text-[11px] text-muted block">{{ $rev->created_at->diffForHumans() }}</span>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    <!-- ================= DISCOVER MORE ================= -->
    <section class="mt-8 bg-surface rounded-3xl border border-warm shadow-sm p-6 sm:p-8">
        <h2 class="text-lg sm:text-xl font-extrabold text-ink">Keşfetmeye Devam Edin</h2>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
               class="inline-flex items-center gap-1.5 rounded-full border border-warm bg-sand hover:bg-surface px-4 py-2 text-xs font-bold text-ink">
                <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
                {{ $restaurant->city->name }} restoranları
            </a>
            @foreach($restaurant->categories as $cat)
                <a href="{{ route('restaurants.index', ['category' => $cat->slug]) }}"
                   class="rounded-full border border-warm bg-sand hover:bg-surface px-4 py-2 text-xs font-bold text-ink">
                    {{ $cat->name }}
                </a>
            @endforeach
            <a href="{{ route('map') }}"
               class="inline-flex items-center gap-1.5 rounded-full border border-warm bg-sand hover:bg-surface px-4 py-2 text-xs font-bold text-ink">
                <x-ico name="map" class="w-3.5 h-3.5 text-terracotta" />
                Tüm mekanlar haritada
            </a>
        </div>
    </section>

    <!-- Related restaurants -->
    @if($relatedRestaurants->isNotEmpty())
        <section class="mt-8">
            <div class="flex items-end justify-between gap-3 mb-5">
                <div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-ink">{{ $restaurant->city->name }} çevresindekiler</h2>
                    <p class="text-xs text-muted mt-0.5">Aynı şehirdeki doğrulanmış diğer mekanlar</p>
                </div>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
                   class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                    <span>Tümünü gör</span>
                    <x-ico name="chevron-right" class="w-3.5 h-3.5" />
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($relatedRestaurants as $rel)
                    <x-restaurant-card :restaurant="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>

@endsection
