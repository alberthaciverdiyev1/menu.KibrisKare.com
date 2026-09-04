<!-- ================= SUB-NAVIGATION TABS ================= -->
<div id="genel-bakis" class="mt-6 border-b border-stone-200/70 sticky top-[72px] z-20 bg-sand/95 backdrop-blur-md -mx-2 px-2 sm:mx-0 sm:px-0">
    <div class="flex items-center gap-6 sm:gap-8 overflow-x-auto hide-scrollbar text-xs sm:text-sm whitespace-nowrap font-semibold">
        <a href="#genel-bakis" class="inline-flex items-center gap-2 py-3 border-b-2 border-terracotta text-terracotta font-bold">
            <x-ico name="clock" class="w-4 h-4" />
            <span>Genel Bakış</span>
        </a>
        <a href="{{ route('restaurant.menu', $restaurant->slug) }}" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-muted hover:text-ink transition-colors">
            <x-ico name="book-open" class="w-4 h-4" />
            <span>Menü</span>
        </a>
        @if($restaurant->branches->isNotEmpty())
            <a href="#subeler" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-muted hover:text-ink transition-colors">
                <x-ico name="map" class="w-4 h-4" />
                <span>Şubeler ({{ $restaurant->branches->count() }})</span>
            </a>
        @endif
        <a href="#konum" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-muted hover:text-ink transition-colors">
            <x-ico name="map-pin" class="w-4 h-4" />
            <span>Konum</span>
        </a>
        <a href="#degerlendirmeler" class="inline-flex items-center gap-2 py-3 border-b-2 border-transparent text-muted hover:text-ink transition-colors">
            <x-ico name="star" class="w-4 h-4" />
            <span>Değerlendirmeler</span>
        </a>
    </div>
</div>
