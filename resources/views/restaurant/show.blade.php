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

    // Build complete photo list with valid storage/http URLs
    $rawPhotos = [];
    if ($restaurant->image) {
        $rawPhotos[] = $restaurant->image;
    }
    if (is_array($restaurant->gallery)) {
        foreach ($restaurant->gallery as $gImg) {
            if (!empty($gImg)) {
                $rawPhotos[] = $gImg;
            }
        }
    }
    $allPhotos = array_values(array_filter(array_map(function($img) {
        if (empty($img)) return null;
        return \Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '/']) ? $img : asset('storage/' . $img);
    }, $rawPhotos)));

    if (empty($allPhotos)) {
        $allPhotos = ['https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=1200&q=80'];
    }
    $totalPhotos = count($allPhotos);
    $hasGallery = $totalPhotos > 1;

    $mapsUrl = "https://www.google.com/maps/dir/?api=1&destination={$restaurant->display_latitude},{$restaurant->display_longitude}";
@endphp

<div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-6 pt-3 pb-28"
     x-data="{
        galleryOpen: false,
        galleryIndex: 0,
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
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-1.5 py-2.5 text-xs text-muted font-medium flex-wrap">
        <a href="{{ route('home') }}" class="text-terracotta font-semibold hover:underline">Keşfet</a>
        <span class="text-stone-300">›</span>
        <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" class="hover:text-ink transition-colors">{{ $restaurant->city->name }}</a>
        <span class="text-stone-300">›</span>
        <span class="text-muted">{{ $restaurant->cuisine }}</span>
        <span class="text-stone-300">›</span>
        <span class="text-ink font-bold truncate">{{ $restaurant->name }}</span>
    </nav>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-open/10 text-open text-xs font-semibold flex items-center gap-2 border border-open/20">
            <x-ico name="check" class="w-4 h-4 shrink-0" />
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Hero Photo Grid -->
    @include('restaurant.partials.hero-gallery')

    <!-- Restaurant Header & Meta Info -->
    @include('restaurant.partials.header-meta')

    <!-- Sub-navigation Tabs -->
    @include('restaurant.partials.sub-nav')

    <!-- Main Content Grid (8 cols Left / 4 cols Sidebar) -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">
        
        <!-- Left Content Column -->
        <div class="lg:col-span-8 space-y-8">
            @include('restaurant.partials.about-section')
            @include('restaurant.partials.featured-menu-section')
            @include('restaurant.partials.branches-section')
            @include('restaurant.partials.map-section')
            @include('restaurant.partials.reviews-section')
        </div>

        <!-- Right Sidebar Column -->
        <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
            @include('restaurant.partials.sidebar-contact')
            @include('restaurant.partials.sidebar-hours')
            @include('restaurant.partials.sidebar-claim')
        </aside>
    </div>

    <!-- Related Restaurants Section -->
    @include('restaurant.partials.related-section')

    <!-- Lightbox Modal -->
    @include('restaurant.partials.lightbox-modal')

</div>

<!-- Mobile Floating Action Bar -->
@include('restaurant.partials.mobile-action-bar')

@endsection
