<!-- ================= ÖNE ÇIKAN MENÜ LEZZETLERİ ================= -->
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
           class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:text-terracotta-dark hover:underline">
            <span>Tüm Menüyü Gör</span>
            <x-ico name="chevron-right" class="w-3.5 h-3.5" />
        </a>
    </div>

    <!-- Featured Items List (2 columns on mobile & desktop) -->
    <div class="grid grid-cols-2 gap-2.5 sm:gap-4">
        @foreach($featuredItems as $dish)
            <div class="p-2.5 sm:p-3 rounded-xl bg-sand flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-3.5">
                <img src="{{ $dish->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=200&q=80' }}"
                     alt="{{ $dish->name }}"
                     class="w-full sm:w-16 h-24 sm:h-16 rounded-lg object-cover shrink-0">
                <div class="min-w-0 w-full flex-1">
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
