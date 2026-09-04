<!-- ================= EN YAKIN / BENZER İŞLETMELER ================= -->
@if($relatedRestaurants->isNotEmpty())
    <section class="mt-14 pt-8 border-t border-stone-200/60">
        <div class="flex items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-ink">{{ $restaurant->city->name }} Çevresindeki Mekanlar</h2>
                <p class="text-xs text-muted mt-0.5">Yakındaki diğer popüler mekanlar</p>
            </div>
            <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}"
               class="inline-flex items-center gap-1 text-xs font-bold text-terracotta hover:text-terracotta-dark hover:underline transition-colors">
                <span>Tümünü gör</span>
                <x-ico name="chevron-right" class="w-3.5 h-3.5" />
            </a>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">
            @foreach($relatedRestaurants as $rel)
                <x-restaurant-card :restaurant="$rel" />
            @endforeach
        </div>
    </section>
@endif
