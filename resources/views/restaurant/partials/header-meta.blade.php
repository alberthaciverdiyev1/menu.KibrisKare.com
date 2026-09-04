<!-- ================= RESTAURANT TITLE & SUBTITLE & META ================= -->
<div class="mt-5 space-y-1.5">
    <div class="flex items-center gap-2.5">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-ink tracking-tight">
            {{ $restaurant->name }}
        </h1>
    </div>

    <!-- Rating & Details Meta Line -->
    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-muted pt-1">
        <div class="flex items-center text-star">
            @for($i = 1; $i <= 5; $i++)
                <x-ico name="star" filled class="w-4 h-4 {{ $i <= round($restaurant->rating) ? 'text-star' : 'text-stone-300' }}" />
            @endfor
        </div>
        <span class="font-bold text-ink">{{ number_format($restaurant->rating, 1) }}</span>
        <span class="text-muted">({{ $restaurant->reviews_count }} değerlendirme)</span>
        <span class="text-stone-300">•</span>
        @if($restaurant->price_range)
            <span class="font-bold text-terracotta font-mono">{{ $restaurant->price_range }}</span>
            <span class="text-stone-300">•</span>
        @endif
        <span class="font-medium text-ink">{{ $restaurant->cuisine }}</span>
        <span class="text-stone-300">•</span>
        <span class="font-bold {{ $todayOpen ? 'text-open' : 'text-rose-600' }}">
            {{ $todayOpen ? 'Şu an Açık' : 'Kapalı' }}
        </span>
    </div>
</div>
