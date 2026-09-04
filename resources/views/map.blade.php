@extends('layouts.app')

@section('title', 'Kıbrıs Restoran Haritası — Konuma Göre Keşfedin | AdaMenü')

@section('content')

    <!-- MAP HEADER -->
    <div class="bg-sand border-b border-warm py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
                        Kıbrıs Restoran Haritası
                    </h1>
                    <p class="text-xs text-muted mt-0.5">
                        Kıbrıs genelindeki {{ $restaurants->count() }} mekanı harita üzerinde inceleyin.
                    </p>
                </div>

                <!-- City filter tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 hide-scrollbar text-xs">
                    <x-city-pill :active="!$citySlug" :href="route('map')">
                        Tüm Ada
                    </x-city-pill>
                    @foreach($cities as $c)
                        <x-city-pill :active="$citySlug == $c->slug" :href="route('map', ['city' => $c->slug])">
                            {{ $c->name }}
                        </x-city-pill>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- MAP CONTAINER & SIDEBAR -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- MAP VIEW -->
            <div class="lg:col-span-8">
                <x-map-section :restaurants="$mapData" :selectedCity="$selectedCity" />
            </div>

            <!-- RESTAURANTS LIST SIDEBAR -->
            <div class="lg:col-span-4 space-y-3 max-h-[580px] overflow-y-auto pr-1 hide-scrollbar">
                <div class="flex items-center justify-between sticky top-0 bg-sand py-2 z-10 border-b border-warm">
                    <span class="font-extrabold text-xs uppercase tracking-wider text-ink">Mekanlar ({{ $restaurants->count() }})</span>
                    <span class="text-[11px] text-muted">Konum Sıralı</span>
                </div>

                @foreach($restaurants as $r)
                    <x-restaurant-card :restaurant="$r" variant="compact" />
                @endforeach
            </div>

        </div>
    </div>

@endsection
