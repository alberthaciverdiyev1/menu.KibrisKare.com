@extends('layouts.app')

@section('title', $restaurant->name . ' — Menü, Konum ve Çalışma Saatleri | AdaMenü Kıbrıs')

@section('content')

@php
    $primary = $restaurant->branches->firstWhere('is_main', true) ?? $restaurant->branches->first();
    $hasMultipleBranches = $restaurant->branches->count() > 1;
    $todayOpen = $restaurant->isOpenNow();
    $days = [
        'monday' => 'Pazartesi', 'tuesday' => 'Salı', 'wednesday' => 'Çarşamba',
        'thursday' => 'Perşembe', 'friday' => 'Cuma', 'saturday' => 'Cumartesi', 'sunday' => 'Pazar',
    ];
    $todayKey = strtolower(now()->format('l'));
    $schedule = $primary ?? $restaurant;
    $weekly = is_array($schedule->weekly_hours ?? null) ? $schedule->weekly_hours : ($restaurant->weekly_hours ?? null);
    $allReviews = $restaurant->branches->flatMap->reviews;
    $firstBranchId = ($primary ?? $restaurant->branches->first())->id ?? null;
    $edge = 'px-5 sm:px-10 lg:px-14 xl:px-20';
@endphp

<!-- ================= TOP STRIP (full-bleed) ================= -->
<div class="w-full {{ $edge }} py-6 flex items-center justify-between border-b border-warm">
    <a href="{{ route('restaurants.index') }}"
       class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-muted hover:text-terracotta">
        <x-ico name="chevron-right" class="w-3.5 h-3.5 rotate-180" />
        Restoranlar
    </a>
    @if($restaurant->phone)
        <a href="tel:{{ $restaurant->phone }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-ink hover:text-terracotta">
            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
            {{ $restaurant->phone }}
        </a>
    @endif
</div>

@if(session('success'))
    <p class="w-full {{ $edge }} py-4 flex items-center gap-2 text-sm font-semibold text-open" role="status">
        <x-ico name="check" class="w-4 h-4" />
        {{ session('success') }}
    </p>
@endif

<!-- ================= MASTHEAD (full-bleed) ================= -->
<header class="w-full bg-sand {{ $edge }} py-16 sm:py-20 lg:py-24">
    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1 text-xs font-bold uppercase tracking-[0.22em] text-muted">
        <span class="text-terracotta">{{ $restaurant->city->name }}</span>
        <span class="text-muted/50">·</span>
        <span>{{ $restaurant->cuisine }}</span>
    </div>

    <h1 class="mt-6 max-w-5xl font-display text-[clamp(2.75rem,8vw,6.5rem)] font-medium text-ink leading-[0.96] tracking-tight">
        {{ $restaurant->name }}
    </h1>

    <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm">
        <span class="flex items-center gap-2">
            <x-ico name="star" filled class="w-4 h-4 text-star" />
            <span class="font-bold text-ink">{{ number_format($restaurant->rating, 1) }}</span>
            <span class="text-muted">{{ $restaurant->reviews_count }} değerlendirme</span>
        </span>
        <span class="flex items-center gap-2">
            <span class="w-1.5 h-1.5 rounded-full {{ $todayOpen ? 'bg-open' : 'bg-muted' }}"></span>
            <span class="font-semibold text-ink">{{ $todayOpen ? 'Şu anda açık' : 'Şu anda kapalı' }}</span>
            <span class="text-muted">Bugün {{ $restaurant->getTodayHours() }}</span>
        </span>
    </div>

    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
       class="mt-12 inline-flex items-center gap-3 text-xl sm:text-2xl font-display font-medium text-terracotta hover:text-terracotta-dark">
        Dijital menüyü inceleyin
        <x-ico name="chevron-right" class="w-6 h-6" />
    </a>
</header>

<!-- ================= FEATURE IMAGE (edge-to-edge, no frame) ================= -->
<figure class="w-full">
    <div class="w-full aspect-[16/10] sm:aspect-[21/9] overflow-hidden bg-sand">
        <img src="{{ $restaurant->image }}"
             alt="{{ $restaurant->name }} mekan görseli"
             class="w-full h-full object-cover">
    </div>
    @if(!empty($restaurant->cover_image) && $restaurant->cover_image !== $restaurant->image)
        <div class="w-full aspect-[21/6] overflow-hidden">
            <img src="{{ $restaurant->cover_image }}"
                 alt="{{ $restaurant->name }} ortam görseli"
                 class="w-full h-full object-cover">
        </div>
    @endif
    <figcaption class="w-full {{ $edge }} pt-4 pb-6 text-[11px] uppercase tracking-[0.18em] text-muted">
        {{ $primary->address ?? $restaurant->address }}, {{ $restaurant->city->name }}
    </figcaption>
</figure>

<!-- ================= ABOUT + SPECS (white band) ================= -->
<section class="w-full bg-surface {{ $edge }} py-16 sm:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-12 gap-y-10">

        <div class="lg:col-span-8">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-muted">Mekan</p>
            <p class="mt-5 max-w-3xl font-display text-2xl sm:text-3xl text-ink/90 leading-snug">
                {{ $restaurant->description }}
            </p>
            <p class="mt-8 max-w-2xl text-sm text-muted leading-relaxed">
                Doğrulanmış dijital menü üzerinden güncel porsiyon fiyatlarını görebilir,
                beğendiğiniz çeşitleri masada veya paket serviste tercih edebilirsiniz.
            </p>
        </div>

        <dl class="lg:col-span-4 lg:border-l lg:border-warm lg:pl-12 text-sm">
            <div class="flex justify-between gap-4 py-3 border-b border-warm">
                <dt class="text-muted">Mutfak</dt>
                <dd class="font-semibold text-ink text-right">{{ $restaurant->cuisine }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-3 border-b border-warm">
                <dt class="text-muted">Fiyat seviyesi</dt>
                <dd class="font-mono font-semibold text-ink">{{ $restaurant->price_range }}</dd>
            </div>
            <div class="flex justify-between gap-4 py-3 border-b border-warm">
                <dt class="text-muted">Konum</dt>
                <dd class="font-semibold text-ink text-right">{{ $primary->name ?? $restaurant->name }}</dd>
            </div>
            @if($restaurant->phone)
                <div class="flex justify-between gap-4 py-3 border-b border-warm">
                    <dt class="text-muted">Telefon</dt>
                    <dd class="text-right">
                        <a href="tel:{{ $restaurant->phone }}" class="font-semibold text-ink hover:text-terracotta">{{ $restaurant->phone }}</a>
                    </dd>
                </div>
            @endif
            <div class="flex justify-between gap-4 py-3">
                <dt class="text-muted">Etiketler</dt>
                <dd class="font-semibold text-ink text-right">{{ $restaurant->categories->pluck('name')->take(3)->join(', ') ?: '—' }}</dd>
            </div>
        </dl>

    </div>
</section>

<!-- ================= FEATURED DISHES (printed-menu on sand) ================= -->
@if($featuredItems->isNotEmpty())
    <section class="w-full bg-sand {{ $edge }} py-16 sm:py-20">
        <div class="flex flex-wrap items-end justify-between gap-6 border-b border-warm pb-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-terracotta">Menüden</p>
                <h2 class="mt-2 font-display text-4xl sm:text-5xl font-medium text-ink">Öne çıkan lezzetler</h2>
            </div>
            <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-terracotta hover:text-terracotta-dark">
                Tüm menüye git
                <x-ico name="chevron-right" class="w-4 h-4" />
            </a>
        </div>

        <div class="mt-2">
            @foreach($featuredItems as $dish)
                <article class="grid grid-cols-[96px_1fr_auto] items-center gap-6 sm:gap-8 py-7 border-b border-warm last:border-0">
                    <span class="w-24 h-24 overflow-hidden bg-surface">
                        <img src="{{ $dish->image ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=300&q=80' }}"
                             alt="{{ $dish->name }}" loading="lazy" class="w-full h-full object-cover">
                    </span>
                    <span class="min-w-0">
                        <span class="flex flex-wrap items-center gap-x-3 gap-y-1">
                            <h3 class="font-display text-2xl font-semibold text-ink">{{ $dish->name }}</h3>
                            @if($dish->is_chef_special)
                                <span class="text-[11px] font-bold uppercase tracking-wider text-star">Şefin seçimi</span>
                            @elseif($dish->is_vegetarian)
                                <span class="text-[11px] font-bold uppercase tracking-wider text-open">Vejetaryen</span>
                            @elseif($dish->is_popular)
                                <span class="text-[11px] font-bold uppercase tracking-wider text-terracotta">Çok tercih edilen</span>
                            @endif
                        </span>
                        @if($dish->description)
                            <p class="mt-1.5 max-w-2xl text-sm text-muted leading-relaxed">{{ $dish->description }}</p>
                        @endif
                    </span>
                    <span class="font-mono text-xl sm:text-2xl font-bold text-ink">
                        {{ number_format($dish->price, 0) }} {{ $dish->currency }}
                    </span>
                </article>
            @endforeach
        </div>
    </section>
@endif

<!-- ================= HOURS + LOCATION (white band) ================= -->
<section id="konum" class="w-full bg-surface {{ $edge }} py-16 sm:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-14 gap-y-12">

        <div class="lg:col-span-5">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-muted">Saatler</p>
            <h2 class="mt-2 font-display text-4xl font-medium text-ink">Çalışma saatleri</h2>

            <ul class="mt-8">
                @foreach($days as $key => $name)
                    @php
                        $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                        $isToday = $key === $todayKey;
                        $closed = is_array($cfg) && !empty($cfg['is_closed']);
                        $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . '–' . $cfg['close'] : null;
                        $time = $closed ? 'Kapalı' : ($range ?? ($schedule->opening_hours ?? '10:00–23:00'));
                    @endphp
                    <li class="flex items-baseline justify-between gap-6 py-3.5 border-b border-warm/70 {{ $isToday ? 'text-ink' : 'text-muted' }}">
                        <span class="flex items-baseline gap-3">
                            <span class="font-semibold">{{ $name }}</span>
                            @if($isToday)
                                <span class="text-[10px] font-bold uppercase tracking-wider text-terracotta">Bugün</span>
                            @endif
                        </span>
                        <span class="{{ $closed ? 'italic' : 'font-mono' }}">{{ $time }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-7">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-muted">Konum</p>
            <h2 class="mt-2 font-display text-4xl font-medium text-ink">{{ $primary->name ?? $restaurant->name }}</h2>
            <p class="mt-3 max-w-xl text-sm text-muted">{{ $primary->address ?? $restaurant->address }}</p>

            <div class="mt-7 h-80 sm:h-[420px] w-full bg-sand border border-warm"
                 x-data="{ init() { this.$nextTick(() => { if (typeof L === 'undefined') return;
                     const map = L.map($el, { center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], zoom: 15, scrollWheelZoom: false, zoomControl: false });
                     L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', { maxZoom: 19 }).addTo(map);
                     L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: L.divIcon({ className: 'custom-pin', html: '<div style=\'background:#E85D3F;color:#fff;padding:4px 9px;border-radius:9999px;font-weight:800;font-size:11px;font-family:sans-serif;border:2px solid #fff;\'>★</div>', iconSize: [30,24], iconAnchor: [15,12] }) }).addTo(map);
                 }); } }" x-init="init()"></div>

            <div class="mt-6 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm">
                <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}"
                   target="_blank" rel="noopener"
                   class="inline-flex items-center gap-2 font-semibold text-terracotta hover:text-terracotta-dark">
                    <x-ico name="map-pin" class="w-4 h-4" />
                    Google Haritalar'da aç
                </a>
                @if($restaurant->phone)
                    <a href="tel:{{ $restaurant->phone }}" class="inline-flex items-center gap-2 font-semibold text-ink hover:text-terracotta">
                        <x-ico name="phone" class="w-4 h-4 text-terracotta" />
                        {{ $restaurant->phone }}
                    </a>
                @endif
            </div>
        </div>

    </div>
</section>

<!-- ================= REVIEWS (sand band) ================= -->
<section class="w-full bg-sand {{ $edge }} py-16 sm:py-20">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-x-14 gap-y-12 items-start">

        <div class="lg:col-span-4 lg:border-r lg:border-warm lg:pr-14">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-terracotta">Misafirler ne diyor</p>
            <div class="mt-5 flex items-end gap-4">
                <span class="font-display text-7xl sm:text-8xl font-medium leading-none text-ink">{{ number_format($restaurant->rating, 1) }}</span>
                <span class="pb-1 flex text-star">
                    <x-ico name="star" filled class="w-6 h-6" />
                </span>
            </div>
            <p class="mt-4 text-sm text-muted">{{ $restaurant->reviews_count }} değerlendirme</p>
        </div>

        <div class="lg:col-span-8 space-y-6" x-data="{ showForm: false, rating: 5 }">
            @if($allReviews->isEmpty())
                <p class="max-w-2xl text-base text-muted leading-relaxed">
                    Henüz değerlendirme yapılmamış. Gittiğinizde lezzeti ve ortamı nasıl bulduğunuzu
                    yorumlayarak gelecek misafirlere yol gösterin.
                </p>
            @else
                <div class="divide-y divide-warm">
                    @foreach($allReviews->take(4) as $rev)
                        <article class="py-6">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <span class="font-semibold text-ink">{{ $rev->author_name ?: 'Anonim misafir' }}</span>
                                <span class="flex items-center gap-0.5">
                                    @for($i = 1; $i <= 5; $i++)
                                        <x-ico name="star" filled class="w-4 h-4 {{ $i <= $rev->rating ? 'text-star' : 'text-muted/25' }}" />
                                    @endfor
                                </span>
                            </div>
                            @if($rev->comment)
                                <p class="mt-2 max-w-2xl text-sm text-ink/85 leading-relaxed">{{ $rev->comment }}</p>
                            @endif
                            <p class="mt-2 text-xs text-muted">{{ $rev->created_at->diffForHumans() }}</p>
                        </article>
                    @endforeach
                </div>
            @endif

            <button type="button" @click="showForm = !showForm"
                    class="text-base font-semibold text-terracotta hover:text-terracotta-dark border-b border-terracotta/30 pb-0.5">
                <span x-text="showForm ? 'Formu kapat' : 'Siz de bir değerlendirme bırakın'"></span>
            </button>

            <form id="review-form" x-show="showForm" x-cloak method="POST"
                  action="{{ $firstBranchId ? route('branches.reviews.store', $firstBranchId) : '#' }}"
                  class="space-y-6 border-t border-warm pt-8">
                @csrf
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-ink">Puanınız</span>
                    <template x-for="s in [1,2,3,4,5]" :key="s">
                        <button type="button"
                                @click="rating = s"
                                :aria-label="'Puan ' + s"
                                :class="s <= rating ? 'text-star' : 'text-muted/25'"
                                class="focus:outline-none">
                            <x-ico name="star" filled class="w-7 h-7" />
                        </button>
                    </template>
                    <input type="hidden" name="rating" :value="rating">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-7">
                    <div>
                        <label for="review-author" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Adınız / rumuz</label>
                        <input id="review-author" type="text" name="author_name" placeholder="Anonim misafir"
                               class="w-full bg-transparent border-0 border-b border-warm px-0 py-2 text-base text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60">
                    </div>
                    <div>
                        <label for="review-branch" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Şube</label>
                        @if($hasMultipleBranches)
                            <select id="review-branch"
                                    @change="document.getElementById('review-form').action = $event.target.selectedOptions[0].dataset.url"
                                    class="w-full bg-transparent border-0 border-b border-warm px-0 py-2 text-base text-ink focus:outline-none focus:border-terracotta">
                                @foreach($restaurant->branches as $b)
                                    <option value="{{ $b->id }}" data-url="{{ route('branches.reviews.store', $b->id) }}" {{ $b->is_main ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        @else
                            <p class="py-2 text-sm text-muted">{{ $primary->name }}</p>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="review-comment" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Yorumunuz</label>
                    <textarea id="review-comment" name="comment" rows="3"
                              placeholder="Lezzet, servis ve ortam nasıldı?"
                              class="w-full bg-transparent border-0 border-b border-warm px-0 py-2 text-base text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60 resize-none"></textarea>
                </div>

                <div class="flex items-center justify-end gap-8">
                    <button type="button" @click="showForm = false" class="text-sm text-muted hover:text-ink">İptal</button>
                    <button type="submit"
                            class="px-8 py-3 text-sm font-bold text-white bg-terracotta hover:bg-terracotta-dark">
                        Değerlendirmeyi gönder
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>

<!-- ================= RELATED (full-bleed images) ================= -->
@if($relatedRestaurants->isNotEmpty())
    <section class="w-full {{ $edge }} py-16 sm:py-20">
        <div class="flex flex-wrap items-end justify-between gap-6 border-b border-warm pb-6">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-terracotta">Yakınlarda</p>
                <h2 class="mt-2 font-display text-4xl sm:text-5xl font-medium text-ink">
                    {{ $restaurant->city->name }} çevresinde keşfedin
                </h2>
            </div>
            <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
               class="inline-flex items-center gap-1.5 text-sm font-semibold text-terracotta hover:text-terracotta-dark">
                Tümünü gör
                <x-ico name="chevron-right" class="w-4 h-4" />
            </a>
        </div>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-14">
            @foreach($relatedRestaurants as $rel)
                <a href="{{ route('restaurant.show', $rel->slug) }}" class="group block">
                    <span class="block aspect-[4/3] overflow-hidden bg-sand">
                        <img src="{{ $rel->image }}" alt="{{ $rel->name }}" loading="lazy"
                             class="w-full h-full object-cover">
                    </span>
                    <span class="mt-5 flex items-baseline justify-between gap-4">
                        <span class="font-display text-2xl font-semibold text-ink group-hover:text-terracotta">{{ $rel->name }}</span>
                        <span class="font-mono text-sm text-muted">{{ $rel->price_range }}</span>
                    </span>
                    <span class="mt-2 flex items-center gap-4 text-sm text-muted">
                        <span class="flex items-center gap-1.5 text-ink font-semibold">
                            <x-ico name="star" filled class="w-4 h-4 text-star" />
                            {{ number_format($rel->rating, 1) }}
                        </span>
                        <span>{{ $rel->cuisine }}</span>
                        <span>{{ $rel->city->name }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endif

@endsection
