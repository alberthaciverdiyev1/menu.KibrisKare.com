@extends('layouts.app')

@section('title', $restaurant->name . " — Detaylar ve Menü | AdaMenü Kıbrıs")

@section('content')

    <!-- RESTAURANT HERO BANNER (Solid dark overlay, no gradient) -->
    <div class="relative bg-ink text-white overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover opacity-25">
            <div class="absolute inset-0 bg-ink/85"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-14">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8">
                <div class="space-y-4 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <x-badge variant="terracotta">{{ $restaurant->city->name }}</x-badge>
                        <x-badge variant="closed">{{ $restaurant->cuisine }}</x-badge>
                        @if($restaurant->is_open)
                            <x-badge variant="open">
                                <span class="w-2 h-2 rounded-full bg-surface animate-pulse"></span>
                                Şimdi Açık ({{ $restaurant->opening_hours }})
                            </x-badge>
                        @else
                            <x-badge variant="closed">Kapalı</x-badge>
                        @endif
                    </div>

                    <h1 class="text-3xl sm:text-5xl md:text-6xl font-black tracking-tight text-white leading-tight">
                        {{ $restaurant->name }}
                    </h1>

                    <p class="text-sm sm:text-base text-stone-200 leading-relaxed font-normal">
                        {{ $restaurant->description }}
                    </p>

                    <div class="flex flex-wrap items-center gap-4 text-xs sm:text-sm text-stone-200 pt-2 font-medium">
                        <div class="flex items-center gap-1.5 font-bold text-white">
                            <span class="text-star text-base">★</span>
                            <span class="text-base font-black">{{ number_format($restaurant->rating, 1) }}</span>
                            <span class="text-stone-300 font-normal">({{ $restaurant->reviews_count }} değerlendirme)</span>
                        </div>
                        <span>•</span>
                        <div>
                            <span>Mesafe: <strong class="text-white font-bold">{{ $restaurant->distance }}</strong></span>
                        </div>
                        <span>•</span>
                        <div>
                            Fiyat Aralığı: <strong class="text-terracotta font-extrabold">{{ $restaurant->price_range }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Action Button to Menu -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-black text-sm uppercase tracking-wider shadow-md hover:shadow-lg">
                        <span>Menüyü Görüntüle</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>

                    @if($restaurant->phone)
                        <a href="tel:{{ $restaurant->phone }}" class="inline-flex items-center justify-center gap-2 px-5 py-4 rounded-xl bg-stone-900 border border-stone-700 hover:bg-stone-800 text-white font-bold text-sm">
                            <span>Ara: {{ $restaurant->phone }}</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN BODY -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
            
            <!-- LEFT 8 COLS: Featured Dishes & Story -->
            <div class="lg:col-span-8 space-y-12">

                <!-- MENU TEASER BANNER -->
                <div class="bg-white rounded-2xl p-8 border border-warm shadow-xs flex flex-col sm:flex-row items-center justify-between gap-6">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-terracotta">Dijital Menü & Fiyat Listesi</span>
                        <h3 class="text-2xl font-black text-ink">Yemekler ve Fiyatlar</h3>
                        <p class="text-sm text-muted max-w-md leading-relaxed">
                            Restoranın tüm başlangıç, ana yemek, tatlı ve içeceklerini güncel porsiyon fiyatlarıyla inceleyin.
                        </p>
                    </div>
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="px-6 py-3.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm uppercase tracking-wider shrink-0 shadow-xs">
                        Menü Sayfasına Git →
                    </a>
                </div>

                <!-- ÖNE ÇIKAN YEMEKLER LİSTESİ -->
                @if(isset($featuredItems) && $featuredItems->isNotEmpty())
                    <div class="space-y-5">
                        <x-section-header 
                            eyebrow="Popüler Seçenekler" 
                            title="Öne Çıkan Lezzetler" 
                            :actionText="'Tüm Menü ('.$restaurant->menuItems->count().' Çeşit)'" 
                            :actionUrl="route('restaurant.menu', $restaurant->slug)" 
                        />

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($featuredItems as $dish)
                                <x-menu-item-card :dish="$dish" :showMenuLink="true" :slug="$restaurant->slug" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- MEKAN HAKKINDA -->
                <div class="space-y-4 pt-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block">Restoran Detayı</span>
                    <h2 class="text-2xl font-black text-ink">Mekan Hakkında</h2>
                    <p class="text-sm text-muted leading-relaxed max-w-2xl font-normal">
                        {{ $restaurant->description }}
                    </p>

                    <!-- Özellikler / Amenities -->
                    <div class="pt-6 border-t border-warm">
                        <span class="text-xs font-bold uppercase tracking-wider text-muted block mb-3">Mekan Özellikleri</span>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs font-bold text-ink">
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Açık Hava & Teras
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Ücretsiz Wi-Fi
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Otopark Alanı
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Kredi Kartı Geçerli
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Paket Servis
                            </div>
                            <div class="flex items-center gap-2 bg-white border border-warm p-3 rounded-lg">
                                <span class="text-open font-extrabold">✓</span> Rezervasyon İmkanı
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- RIGHT 4 COLS: Info Sidebar (Address, Hours, Location) -->
            <div class="lg:col-span-4 space-y-6">

                <!-- İletişim ve Konum -->
                <div class="bg-white rounded-2xl p-6 border border-warm shadow-xs space-y-5">
                    <h3 class="font-black text-sm text-ink uppercase tracking-wider border-b border-warm pb-2">
                        Konum ve İletişim
                    </h3>

                    <div class="space-y-4 text-xs sm:text-sm">
                        <div>
                            <span class="text-muted uppercase tracking-wider block font-bold text-[11px]">Adres:</span>
                            <p class="font-semibold text-ink mt-1 leading-relaxed">{{ $restaurant->address }}</p>
                        </div>

                        <div>
                            <span class="text-muted uppercase tracking-wider block font-bold text-[11px]">Bölge:</span>
                            <p class="font-extrabold text-terracotta mt-0.5">{{ $restaurant->city->name }}, Kuzey Kıbrıs</p>
                        </div>

                        @if($restaurant->phone)
                            <div>
                                <span class="text-muted uppercase tracking-wider block font-bold text-[11px]">Telefon:</span>
                                <a href="tel:{{ $restaurant->phone }}" class="font-bold text-ink hover:text-terracotta block mt-0.5">
                                    {{ $restaurant->phone }}
                                </a>
                            </div>
                        @endif
                    </div>

                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->latitude }},{{ $restaurant->longitude }}" 
                       target="_blank" 
                       class="block w-full py-3.5 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-xs text-center uppercase tracking-wider shadow-xs">
                        Google Haritalarda Aç ↗
                    </a>
                </div>

                <!-- Çalışma Saatleri -->
                <div class="bg-white rounded-2xl p-6 border border-warm shadow-xs space-y-4">
                    <h3 class="font-black text-sm text-ink uppercase tracking-wider border-b border-warm pb-2">
                        Çalışma Saatleri
                    </h3>

                    <ul class="space-y-2 text-xs sm:text-sm">
                        @foreach(['Pazartesi', 'Salı', 'Çarşamba', 'Perşembe', 'Cuma', 'Cumartesi', 'Pazar'] as $day)
                            <li class="flex items-center justify-between py-1 border-b border-warm/60 last:border-none">
                                <span class="text-muted font-medium">{{ $day }}</span>
                                <span class="font-bold text-ink">{{ $restaurant->opening_hours }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

        </div>

        <!-- AYNI ŞEHİRDEKİ DİĞER MEKANLAR -->
        @if($relatedRestaurants->isNotEmpty())
            <div class="mt-20 pt-10 border-t border-warm">
                <div class="mb-8">
                    <span class="text-xs font-bold uppercase tracking-wider text-muted block">Civardaki Seçenekler</span>
                    <h2 class="text-2xl font-black text-ink mt-0.5">
                        {{ $restaurant->city->name }} Bölgesindeki Diğer Restoranlar
                    </h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach($relatedRestaurants as $rel)
                        <x-restaurant-card :restaurant="$rel" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

@endsection
