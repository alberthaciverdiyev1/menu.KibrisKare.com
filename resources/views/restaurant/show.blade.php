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

    <!-- Top utilities -->
    <div class="flex items-center justify-between py-6">
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

    <!-- ================= HERO IMAGE ================= -->
    <div class="rounded-2xl overflow-hidden border border-warm shadow-sm bg-sand">
        <div class="aspect-[16/9] sm:aspect-[21/9] w-full">
            <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
        </div>
    </div>

    <!-- ================= PROFILE (single focal panel) ================= -->
    <section class="bg-surface rounded-2xl border border-warm shadow-sm mt-6">
        <div class="p-6 sm:p-8">
            <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-7">

                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sand border border-warm text-xs font-bold text-ink">
                            <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
                            {{ $restaurant->city->name }}
                        </span>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sand border border-warm text-xs font-bold text-muted">
                            <x-ico name="tag" class="w-3.5 h-3.5" />
                            {{ $restaurant->cuisine }}
                        </span>
                        @if($restaurant->price_range)
                            <span class="px-3 py-1 rounded-full bg-sand border border-warm text-xs font-bold font-mono text-ink">{{ $restaurant->price_range }}</span>
                        @endif
                    </div>

                    <h1 class="mt-4 text-3xl sm:text-4xl font-extrabold text-ink tracking-tight">{{ $restaurant->name }}</h1>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                        <span class="inline-flex items-center gap-1.5 font-bold text-ink">
                            <x-ico name="star" filled class="w-4 h-4 text-star" />
                            {{ number_format($restaurant->rating, 1) }}
                            <span class="font-semibold text-muted">({{ $restaurant->reviews_count }})</span>
                        </span>
                        <span class="inline-flex items-center gap-2 {{ $todayOpen ? 'text-open' : 'text-muted' }}">
                            <span class="w-2 h-2 rounded-full {{ $todayOpen ? 'bg-open' : 'bg-muted' }}"></span>
                            <span class="font-bold">{{ $todayOpen ? 'Şu anda açık' : 'Şu anda kapalı' }}</span>
                        </span>
                        <span class="text-muted">Bugün {{ $restaurant->getTodayHours() }}</span>
                    </div>

                    @if($address)
                        <p class="mt-4 flex items-start gap-2 text-sm text-muted">
                            <x-ico name="map-pin" class="w-4 h-4 text-terracotta shrink-0 mt-0.5" />
                            <span>{{ $address }}{{ $restaurant->city->name ? ', ' . $restaurant->city->name : '' }}</span>
                        </p>
                    @endif
                </div>

                <div class="shrink-0 w-full xl:w-64 space-y-2.5 xl:pt-1">
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                       class="w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-sm">
                        <x-ico name="book-open" class="w-5 h-5" />
                        Dijital Menü ve Fiyatlar
                    </a>
                    <div class="grid grid-cols-2 gap-2.5">
                        @if($restaurant->phone)
                            <a href="tel:{{ $restaurant->phone }}"
                               class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs">
                                <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                                Ara
                            </a>
                        @endif
                        <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                           target="_blank" rel="noopener" aria-label="Yol tarifi al"
                           class="inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-surface hover:bg-sand border border-warm text-ink font-bold text-xs">
                            <x-ico name="map" class="w-4 h-4 text-terracotta" />
                            Yol Tarifi
                        </a>
                    </div>
                    @if($restaurant->phone)
                        <div class="pt-3 mt-1 border-t border-warm">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-muted">Rezervasyon &amp; Sipariş</p>
                            <a href="tel:{{ $restaurant->phone }}"
                               class="mt-1 block text-lg font-extrabold text-ink hover:text-terracotta whitespace-nowrap">{{ $restaurant->phone }}</a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

    <!-- ================= BODY: open on sand, only media is raised ================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-12 gap-y-12 pt-12">

        <!-- LEFT: narrative -->
        <div class="lg:col-span-7 space-y-12">

            <!-- About -->
            <section>
                <h2 class="text-2xl font-extrabold text-ink">Mekan Hakkında</h2>
                <p class="mt-4 text-base sm:text-lg text-ink/80 leading-relaxed">{{ $restaurant->description }}</p>
            </section>

            <!-- Featured dishes (media cards) -->
            @if($featuredItems->isNotEmpty())
                <section>
                    <div class="flex items-end justify-between gap-4">
                        <h2 class="text-2xl font-extrabold text-ink">Öne Çıkan Lezzetler</h2>
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                           class="inline-flex items-center gap-1 text-sm font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                            Tüm menü
                            <x-ico name="chevron-right" class="w-4 h-4" />
                        </a>
                    </div>
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($featuredItems as $dish)
                            <x-menu-item-card :dish="$dish" :showMenuLink="false" />
                        @endforeach
                    </div>
                </section>
            @endif

            <!-- Reviews -->
            <section class="pt-2 border-t border-warm" x-data="{ showForm: false, rating: 5 }">
                <div class="flex flex-wrap items-end justify-between gap-6 pt-2">
                    <div>
                        <h2 class="text-2xl font-extrabold text-ink">Değerlendirmeler</h2>
                        <div class="mt-3 flex items-end gap-3">
                            <span class="text-5xl font-extrabold text-ink">{{ number_format($restaurant->rating, 1) }}</span>
                            <span class="text-star pb-1"><x-ico name="star" filled class="w-6 h-6" /></span>
                            <span class="pb-1 text-muted text-sm">{{ $restaurant->reviews_count }} değerlendirme</span>
                        </div>
                    </div>
                    <button type="button" @click="showForm = !showForm"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-sm">
                        <span x-text="showForm ? 'Formu Kapat' : 'Değerlendirme Bırak'"></span>
                    </button>
                </div>

                @if($allReviews->isNotEmpty())
                    <div class="mt-6 divide-y divide-warm">
                        @foreach($allReviews->take(4) as $rev)
                            <article class="py-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="font-bold text-ink">{{ $rev->author_name ?: 'Anonim misafir' }}</span>
                                    <span class="flex items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <x-ico name="star" filled class="w-4 h-4 {{ $i <= $rev->rating ? 'text-star' : 'text-muted/25' }}" />
                                        @endfor
                                    </span>
                                </div>
                                @if($rev->comment)
                                    <p class="mt-2 text-sm text-ink/85 leading-relaxed">{{ $rev->comment }}</p>
                                @endif
                                <p class="mt-2 text-xs text-muted">{{ $rev->created_at->diffForHumans() }}</p>
                            </article>
                        @endforeach
                    </div>
                @else
                    <p class="mt-6 text-sm text-muted leading-relaxed">
                        Henüz değerlendirme yapılmamış. Gittiğinizde lezzeti ve ortamı değerlendirerek diğer misafirlere yol gösterin.
                    </p>
                @endif

                <form id="review-form" x-show="showForm" x-cloak method="POST"
                      action="{{ $firstBranchId ? route('branches.reviews.store', $firstBranchId) : '#' }}"
                      class="mt-6 space-y-5">
                    @csrf
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-sm font-bold text-ink">Puanınız</span>
                        <template x-for="s in [1,2,3,4,5]" :key="s">
                            <button type="button"
                                    @click="rating = s"
                                    :aria-label="'Puan ' + s"
                                    :class="s <= rating ? 'text-star' : 'text-muted/30'"
                                    class="focus:outline-none">
                                <x-ico name="star" filled class="w-6 h-6" />
                            </button>
                        </template>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="review-author" class="block text-xs font-bold text-muted mb-1.5">Adınız / Rumuz</label>
                            <input id="review-author" type="text" name="author_name" placeholder="Anonim misafir"
                                   class="w-full px-4 py-2.5 rounded-xl bg-white border border-warm text-sm text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60">
                        </div>
                        <div>
                            <label for="review-branch" class="block text-xs font-bold text-muted mb-1.5">Şube</label>
                            @if($hasMultipleBranches)
                                <select id="review-branch"
                                        @change="document.getElementById('review-form').action = $event.target.selectedOptions[0].dataset.url"
                                        class="w-full px-4 py-2.5 rounded-xl bg-white border border-warm text-sm text-ink focus:outline-none focus:border-terracotta">
                                    @foreach($restaurant->branches as $b)
                                        <option value="{{ $b->id }}" data-url="{{ route('branches.reviews.store', $b->id) }}" {{ $b->is_main ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <p class="py-2.5 text-sm text-muted">{{ $primary->name }}</p>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="review-comment" class="block text-xs font-bold text-muted mb-1.5">Yorumunuz</label>
                        <textarea id="review-comment" name="comment" rows="3"
                                  placeholder="Lezzet, servis ve ortam nasıldı?"
                                  class="w-full px-4 py-2.5 rounded-xl bg-white border border-warm text-sm text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60 resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-4">
                        <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-sm font-bold text-muted hover:text-ink">İptal</button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-sm font-bold">Gönder</button>
                    </div>
                </form>
            </section>

        </div>

        <!-- RIGHT: hours + map, open on sand -->
        <aside class="lg:col-span-5 space-y-12">

            <!-- Hours (open list) -->
            <section>
                <h2 class="text-2xl font-extrabold text-ink">Çalışma Saatleri</h2>
                <ul class="mt-5">
                    @foreach($days as $key => $name)
                        @php
                            $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                            $isToday = $key === $todayKey;
                            $closed = is_array($cfg) && !empty($cfg['is_closed']);
                            $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . ' – ' . $cfg['close'] : null;
                            $time = $closed ? 'Kapalı' : ($range ?? ($schedule->opening_hours ?? '10:00 – 23:00'));
                        @endphp
                        <li class="flex items-center justify-between gap-6 py-3 border-b border-warm/70 {{ $isToday ? 'text-ink' : 'text-muted' }}">
                            <span class="flex items-center gap-2">
                                <span class="{{ $isToday ? 'font-bold' : 'font-medium' }}">{{ $name }}</span>
                                @if($isToday)
                                    <span class="px-1.5 py-0.5 rounded bg-terracotta/10 text-terracotta text-[10px] font-bold">Bugün</span>
                                @endif
                            </span>
                            <span class="{{ $closed ? 'italic' : 'font-mono' }}">{{ $time }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>

            <!-- Map (media frame only) -->
            <section>
                <h2 class="text-2xl font-extrabold text-ink">Konum</h2>
                <div class="mt-5 h-64 rounded-2xl overflow-hidden border border-warm bg-white"
                     x-data="{ init() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                         const m = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                         L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(m);
                         L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: L.divIcon({ className: 'custom-pin', html: '<div style=\'background:#E85D3F;color:#fff;padding:4px 8px;border-radius:9999px;font-weight:800;font-size:11px;border:2px solid #fff;\'>★</div>', iconSize: [28,22], iconAnchor: [14,11] }) }).addTo(m);
                     }); } }" x-init="init()"></div>
                <p class="mt-3 text-sm text-muted">{{ $address }}</p>
                <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                   target="_blank" rel="noopener"
                   class="mt-3 inline-flex items-center gap-2 text-sm font-bold text-terracotta hover:text-terracotta-dark">
                    <x-ico name="map-pin" class="w-4 h-4" />
                    Google Haritalar'da aç
                </a>
            </section>

        </aside>
    </div>

    <!-- ================= RELATED (media cards) ================= -->
    @if($relatedRestaurants->isNotEmpty())
        <section class="mt-16 pt-12 border-t border-warm">
            <div class="flex items-end justify-between gap-4">
                <h2 class="text-2xl font-extrabold text-ink">{{ $restaurant->city->name }} çevresindekiler</h2>
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
                   class="inline-flex items-center gap-1 text-sm font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                    Tümünü gör
                    <x-ico name="chevron-right" class="w-4 h-4" />
                </a>
            </div>
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($relatedRestaurants as $rel)
                    <x-restaurant-card :restaurant="$rel" />
                @endforeach
            </div>
        </section>
    @endif

</div>

@endsection
