@props([
    'cities' => [],
    'selectedCity' => null,
    'currentCity' => null
])

<!-- TOP ANNOUNCEMENT BAR -->
<div class="bg-ink text-sand text-xs font-medium py-2 px-4 border-b border-stone-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-open"></span>
            <span class="font-semibold text-white">Kıbrıs Restoran Rehberi:</span>
            <span class="text-stone-300">Girne, Lefkoşa, Gazimağusa, İskele mekanları ve güncel menüleri</span>
        </div>
        <div class="hidden sm:flex items-center gap-4 text-stone-300">
            <a href="{{ route('map') }}" class="hover:text-white transition-colors">Haritada Keşfet</a>
            <span>•</span>
            <a href="/admin" class="hover:text-white transition-colors">İşletme Girişi</a>
        </div>
    </div>
</div>

<!-- MAIN NAVBAR (Clean White Surface, Terracotta Accents) -->
<header class="sticky top-0 z-40 bg-surface/95 backdrop-blur-md border-b border-warm shadow-xs transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-18">
            
            <!-- BRAND LOGO -->
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-terracotta text-white flex items-center justify-center font-black text-lg shadow-sm group-hover:bg-terracotta-dark transition-colors">
                        M
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="font-extrabold text-xl tracking-tight text-ink">ADA<span class="text-terracotta">MENÜ</span></span>
                            <span class="text-[10px] font-bold tracking-wider uppercase bg-sand text-muted border border-warm px-1.5 py-0.5 rounded">Kıbrıs</span>
                        </div>
                    </div>
                </a>

                <!-- City Selector -->
                <div class="hidden lg:block relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" type="button" class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-ink bg-sand hover:bg-surface rounded-lg transition-colors border border-warm">
                        <span class="w-2 h-2 rounded-full bg-terracotta"></span>
                        <span>{{ $selectedCity->name ?? $currentCity->name ?? 'Tüm Kıbrıs' }}</span>
                        <svg class="w-3.5 h-3.5 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open" x-cloak class="absolute left-0 mt-2 w-48 bg-surface rounded-xl shadow-xl border border-warm py-1.5 z-50">
                        <div class="px-3 py-1.5 text-[11px] font-bold text-muted uppercase tracking-wider">Şehir Seçin</div>
                        <a href="{{ route('restaurants.index') }}" class="block px-3 py-2 text-xs font-semibold text-ink hover:bg-sand">Tüm Kıbrıs</a>
                        <div class="h-px bg-warm my-1"></div>
                        @foreach($cities ?? [] as $city)
                            <a href="{{ route('restaurants.index', ['city' => $city->slug]) }}" class="block px-3 py-2 text-xs font-semibold text-muted hover:bg-sand hover:text-ink {{ (isset($selectedCity) && $selectedCity->slug == $city->slug) || (isset($currentCity) && $currentCity->slug == $city->slug) ? 'font-bold text-terracotta bg-orange-50/50' : '' }}">
                                {{ $city->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- NAVIGATION (Discover, Map, Categories) -->
            <nav class="hidden md:flex items-center space-x-1 lg:space-x-2">
                <a href="{{ route('restaurants.index') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('restaurants.index') ? 'text-terracotta bg-orange-50/60' : 'text-ink hover:text-terracotta hover:bg-sand' }}">
                    Keşfet (Discover)
                </a>
                <a href="{{ route('map') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('map') ? 'text-terracotta bg-orange-50/60' : 'text-ink hover:text-terracotta hover:bg-sand' }}">
                    Harita (Map)
                </a>
                <a href="{{ route('categories') }}" class="px-3.5 py-2 rounded-lg text-sm font-bold transition-colors {{ request()->routeIs('categories') ? 'text-terracotta bg-orange-50/60' : 'text-ink hover:text-terracotta hover:bg-sand' }}">
                    Kategoriler
                </a>
            </nav>

            <!-- RIGHT SIDE ACTIONS: Login & CTA -->
            <div class="flex items-center space-x-3">
                <a href="/admin/login" class="hidden sm:inline-flex items-center text-xs font-bold text-muted hover:text-ink px-2 py-1 transition-colors">
                    Restoran Girişi
                </a>
                <a href="{{ route('restaurants.index') }}" class="inline-flex items-center px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold bg-terracotta hover:bg-terracotta-dark text-white transition-colors shadow-xs">
                    Restoranları Keşfet
                </a>
            </div>

        </div>
    </div>
</header>
