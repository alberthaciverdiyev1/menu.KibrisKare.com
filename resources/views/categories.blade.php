@extends('layouts.app')

@section('title', 'Kıbrıs Lezzet ve Mutfak Türleri | AdaMenü')

@section('content')

    <!-- CATEGORIES HEADER -->
    <div class="bg-sand border-b border-warm py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl sm:text-4xl font-black text-ink tracking-tight">
                Mutfak ve Lezzet Türleri
            </h1>
            <p class="text-sm text-muted mt-1 font-normal">
                Kıbrıs'ın geleneksel kebaplarından taze deniz mahsullerine, taş fırın pizzalarından dünya mutfağına kategorileri keşfedin.
            </p>
        </div>
    </div>

    <!-- CATEGORIES GRID & RESTAURANTS -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3.5">
            @foreach($categories as $cat)
                <x-category-card :category="$cat" />
            @endforeach
        </div>

        <!-- DETAILED CATEGORY SECTIONS -->
        <div class="space-y-14">
            @foreach($categories as $cat)
                @if($cat->restaurants->isNotEmpty())
                    <div class="space-y-4">
                        <x-section-header 
                            eyebrow="Kategori Seçkisi" 
                            :title="$cat->name . ' Restoranları'" 
                            :actionText="'Tümünü Gör ('.$cat->restaurants_count.')'" 
                            :actionUrl="route('restaurants.index', ['category' => $cat->slug])" 
                        />

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            @foreach($cat->restaurants as $rest)
                                <x-restaurant-card :restaurant="$rest" />
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

    </div>

@endsection
