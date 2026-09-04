@extends('layouts.app')

@section('title', $restaurant->name . ' — Dijital Menü ve Fiyat Listesi | AdaMenü')

@section('content')

@php
    $itemCount = $restaurant->menuCategories->sum(fn($c) => $c->items->count());
    $cityName = ($currentBranch && $currentBranch->city) ? $currentBranch->city->name : $restaurant->city->name;
    $phoneToCall = ($currentBranch && $currentBranch->phone) ? $currentBranch->phone : $restaurant->phone;
    $edge = 'px-5 sm:px-10 lg:px-14 xl:px-20';
@endphp

<!-- ================= TOP STRIP ================= -->
<div class="w-full {{ $edge }} py-6 flex items-center justify-between border-b border-warm">
    <a href="{{ route('restaurant.show', $restaurant->slug) }}"
       class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.18em] text-muted hover:text-terracotta">
        <x-ico name="chevron-right" class="w-3.5 h-3.5 rotate-180" />
        Mekan detayına dön
    </a>
    @if($phoneToCall)
        <a href="tel:{{ $phoneToCall }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-ink hover:text-terracotta">
            <x-ico name="phone" class="w-4 h-4 text-terracotta" />
            {{ $phoneToCall }}
        </a>
    @endif
</div>

@if(session('success'))
    <p class="w-full {{ $edge }} py-4 flex items-center gap-2 text-sm font-semibold text-open" role="status">
        <x-ico name="check" class="w-4 h-4" />
        {{ session('success') }}
    </p>
@endif

<!-- ================= MASTHEAD ================= -->
<header class="w-full bg-sand {{ $edge }} py-14 sm:py-16 lg:py-20">
    <p class="text-xs font-bold uppercase tracking-[0.22em] text-terracotta">
        Dijital menü · {{ $cityName }}
    </p>
    <h1 class="mt-5 max-w-5xl font-display text-[clamp(2.5rem,7vw,5.5rem)] font-medium text-ink leading-[0.98] tracking-tight">
        {{ $restaurant->name }}
    </h1>

    <div class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-3 text-sm text-muted">
        <span class="flex items-center gap-2 text-ink">
            <x-ico name="star" filled class="w-4 h-4 text-star" />
            <span class="font-bold">{{ number_format($restaurant->rating, 1) }}</span>
            <span class="text-muted">{{ $restaurant->reviews_count }} değerlendirme</span>
        </span>
        <span class="text-muted">{{ $restaurant->cuisine }}</span>
        <span class="text-muted">{{ $itemCount }} çeşit</span>
        @if($currentBranch)
            <span class="inline-flex items-center gap-2">
                <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
                {{ $currentBranch->name }}
                <span class="text-muted">{{ $currentBranch->getTodayHours() }}</span>
            </span>
        @else
            <span class="text-muted">{{ $restaurant->getTodayHours() }}</span>
        @endif
    </div>
</header>

<!-- ================= CONTROLS (sticky editorial) ================= -->
<div class="w-full bg-surface {{ $edge }} sticky top-[72px] z-30 py-4 border-b border-warm"
     x-data="{
        activeCategory: 'all',
        searchQuery: '',
        matchesFilter(item, categoryId) {
            if (this.activeCategory !== 'all' && this.activeCategory != categoryId) return false;
            const q = this.searchQuery.toLowerCase().trim();
            if (!q) return true;
            return item.name.toLowerCase().includes(q) || (item.desc && item.desc.toLowerCase().includes(q));
        }
     }">

    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
        <div class="relative sm:max-w-xs w-full">
            <label for="menu-search" class="sr-only">Menüde ara</label>
            <x-ico name="search" class="w-4 h-4 absolute left-0 top-1/2 -translate-y-1/2 text-muted" />
            <input id="menu-search"
                   type="text"
                   x-model="searchQuery"
                   placeholder="Menüde ara…"
                   class="w-full pl-7 pr-8 bg-transparent border-b border-warm focus:border-terracotta py-1.5 text-sm text-ink placeholder:text-muted/60 focus:outline-none font-medium">
            <button type="button"
                    x-show="searchQuery"
                    @click="searchQuery = ''"
                    aria-label="Aramayı temizle"
                    class="absolute right-0 top-1/2 -translate-y-1/2 text-muted hover:text-ink">
                <x-ico name="close" class="w-4 h-4" />
            </button>
        </div>
    </div>

    <div class="flex items-center gap-6 overflow-x-auto hide-scrollbar mt-3 text-sm whitespace-nowrap">
        <button type="button"
                @click="activeCategory = 'all'"
                :class="activeCategory === 'all' ? 'text-terracotta border-terracotta' : 'text-muted border-transparent hover:text-ink'"
                class="shrink-0 font-bold uppercase tracking-wider border-b-2 pb-1.5">
            Tümü
            <span class="font-normal normal-case tracking-normal">{{ $itemCount }}</span>
        </button>
        @foreach($restaurant->menuCategories as $cat)
            <button type="button"
                    @click="activeCategory = '{{ $cat->id }}'"
                    :class="activeCategory === '{{ $cat->id }}' ? 'text-terracotta border-terracotta' : 'text-muted border-transparent hover:text-ink'"
                    class="shrink-0 font-bold uppercase tracking-wider border-b-2 pb-1.5">
                {{ $cat->name }}
                <span class="font-normal normal-case tracking-normal">{{ $cat->items->count() }}</span>
            </button>
        @endforeach
    </div>
</div>

<!-- ================= MENU (printed-list rows, full-bleed) ================= -->
<section class="w-full bg-surface {{ $edge }} py-12 sm:py-16 space-y-14">
    @foreach($restaurant->menuCategories as $cat)
        <div x-show="activeCategory === 'all' || activeCategory === '{{ $cat->id }}'" x-cloak>
            <div class="flex items-baseline justify-between gap-6 pb-4 border-b-2 border-ink/10">
                <h2 class="font-display text-3xl sm:text-4xl font-medium text-ink">{{ $cat->name }}</h2>
                <span class="font-mono text-sm text-muted">{{ $cat->items->count() }}</span>
            </div>

            <div class="mt-2">
                @foreach($cat->items as $dish)
                    <article class="grid grid-cols-[80px_1fr_auto] sm:grid-cols-[96px_1fr_auto] items-center gap-5 sm:gap-8 py-6 border-b border-warm last:border-0"
                             x-show="matchesFilter({
                                 name: {{ json_encode($dish->name) }},
                                 desc: {{ json_encode($dish->description ?? '') }}
                             }, '{{ $cat->id }}')">

                        <span class="w-20 h-20 sm:w-24 sm:h-24 overflow-hidden bg-sand shrink-0">
                            <img src="{{ $dish->image ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=300&q=80' }}"
                                 alt="{{ $dish->name }}" loading="lazy" class="w-full h-full object-cover">
                        </span>

                        <span class="min-w-0">
                            <span class="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <h3 class="font-display text-xl sm:text-2xl font-semibold text-ink">{{ $dish->name }}</h3>
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

                        <span class="font-mono text-lg sm:text-2xl font-bold text-ink shrink-0">
                            {{ number_format($dish->price, 0) }} {{ $dish->currency }}
                        </span>
                    </article>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Footnote -->
    <p class="pt-4 text-center text-[11px] uppercase tracking-[0.18em] text-muted/70">
        Fiyatlar işletme tarafından belirlenir · Porsiyon bilgisi için servis görevlisine danışın
    </p>
</section>

<!-- ================= BRANCH REVIEW (when viewing a branch menu) ================= -->
@if($currentBranch)
    <section class="w-full bg-sand {{ $edge }} py-16 sm:py-20 border-t border-warm">
        <div class="max-w-3xl">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-terracotta">Deneyiminiz</p>
            <h2 class="mt-2 font-display text-4xl font-medium text-ink">Bu şubeyi değerlendirin</h2>
            <p class="mt-3 text-sm text-muted leading-relaxed">{{ $currentBranch->name }} şubesindeki yemeği ve servisi anonim olarak puanlayın.</p>

            <div class="mt-8 space-y-5" x-data="{ showForm: false, rating: 5 }">
                @if($currentBranch->reviews->isNotEmpty())
                    <div class="divide-y divide-warm">
                        @foreach($currentBranch->reviews->take(3) as $rev)
                            <article class="py-5">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <span class="font-semibold text-ink">{{ $rev->author_name ?: 'Anonim misafir' }}</span>
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
                    <p class="text-sm text-muted">Henüz yorum yapılmamış. Gittiğinizde nasıl bulduğunuzu paylaşın.</p>
                @endif

                <button type="button" @click="showForm = !showForm"
                        class="text-base font-semibold text-terracotta hover:text-terracotta-dark border-b border-terracotta/30 pb-0.5">
                    <span x-text="showForm ? 'Formu kapat' : 'Puan ve yorum bırakın'"></span>
                </button>

                <form x-show="showForm" x-cloak method="POST"
                      action="{{ route('branches.reviews.store', $currentBranch->id) }}"
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

                    <div>
                        <label for="rev-author" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Adınız / rumuz</label>
                        <input id="rev-author" type="text" name="author_name" placeholder="Anonim misafir"
                               class="w-full bg-transparent border-0 border-b border-warm px-0 py-2 text-base text-ink focus:outline-none focus:border-terracotta placeholder:text-muted/60">
                    </div>

                    <div>
                        <label for="rev-comment" class="block text-xs font-bold uppercase tracking-wider text-muted mb-2">Yorumunuz</label>
                        <textarea id="rev-comment" name="comment" rows="3"
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
@endif

@endsection
