<!-- ================= MOBILE STICKY FLOATING ACTION BAR ================= -->
<div class="sm:hidden fixed bottom-0 left-0 right-0 z-40 bg-surface/95 backdrop-blur-md border-t border-stone-200/80 p-3 px-4 flex items-center gap-2 shadow-2xl">
    @if($restaurant->phone)
        <a href="tel:{{ $restaurant->phone }}"
           class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-sand text-ink font-bold text-xs">
            <x-ico name="phone" class="w-3.5 h-3.5 text-terracotta" />
            <span>Ara</span>
        </a>
    @endif
    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
       class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-sand text-ink font-bold text-xs">
        <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta" />
        <span>Yol Tarifi</span>
    </a>
    <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
       class="flex-[1.5] inline-flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-terracotta text-white font-bold text-xs shadow-md">
        <x-ico name="book-open" class="w-3.5 h-3.5" />
        <span>Menü</span>
    </a>
</div>
