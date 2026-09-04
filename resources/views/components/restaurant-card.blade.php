@props([
    'restaurant',
    'variant' => 'default' // 'default' or 'compact'
])

@if($variant === 'compact')
    <!-- COMPACT RESTAURANT CARD (Sidebar / Map list) -->
    <div class="group bg-surface rounded-xl p-3 border border-warm hover:border-muted flex items-center gap-3 shadow-xs hover:shadow-sm">
        <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="w-16 h-16 rounded-lg overflow-hidden shrink-0 bg-sand block relative">
            <img src="{{ $restaurant->image }}" 
                 alt="{{ $restaurant->name }}" 
                 loading="lazy" 
                 class="w-full h-full object-cover">
        </a>
        <div class="flex-grow min-w-0">
            <div class="flex items-center justify-between gap-1">
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="truncate">
                    <h4 class="font-bold text-xs text-ink truncate group-hover:text-terracotta">
                        {{ $restaurant->name }}
                    </h4>
                </a>
            </div>

            <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                <span class="font-bold text-star flex items-center gap-0.5">
                    <x-ico name="star" filled class="w-3 h-3" />
                    <span>{{ number_format($restaurant->rating, 1) }}</span>
                </span>
                <span>•</span>
                <span class="truncate">{{ $restaurant->city->name }}</span>
                <span>•</span>
                @if($restaurant->isOpenNow())
                    <span class="text-open font-bold text-[10px]">Açık</span>
                @else
                    <span class="text-muted font-medium text-[10px]">Kapalı</span>
                @endif
            </div>

            <div class="flex items-center gap-2 mt-2">
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" 
                   class="text-[10px] font-bold text-muted hover:text-ink bg-sand border border-warm px-2 py-0.5 rounded">
                    Detay
                </a>
                <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                   class="text-[10px] font-bold text-terracotta hover:text-terracotta-dark">
                    Menüyü Gör →
                </a>
            </div>
        </div>
    </div>
@else
    <!-- STANDARD FULL RESTAURANT CARD (Single unified card everywhere) -->
    <div class="group bg-surface rounded-xl sm:rounded-2xl border border-warm hover:border-muted overflow-hidden flex flex-col shadow-xs hover:shadow-md">
        
        <!-- IMAGE CONTAINER -->
        <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="relative aspect-[16/10] w-full overflow-hidden bg-sand block">
            <img src="{{ $restaurant->image }}" 
                 alt="{{ $restaurant->name }}" 
                 loading="lazy" 
                 class="w-full h-full object-cover group-hover:scale-103 transition-transform duration-500">

            <!-- Rating (Top Left) -->
            <div class="absolute top-2 left-2 sm:top-3 sm:left-3 flex items-center gap-1">
                <span class="inline-flex items-center gap-1 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md text-[10px] sm:text-xs font-extrabold bg-ink text-white shadow-xs border border-stone-700/40">
                    <x-ico name="star" filled class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-star" />
                    <span>{{ number_format($restaurant->rating, 1) }}</span>
                    <span class="text-stone-300 text-[9px] sm:text-[11px] font-medium">({{ $restaurant->reviews_count }})</span>
                </span>
            </div>

            <!-- Location & Status (Bottom Solid Badges) -->
            <div class="absolute bottom-2 left-2 right-2 sm:bottom-3 sm:left-3 sm:right-3 flex items-center justify-between text-[9px] sm:text-xs gap-1">
                <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md font-bold bg-ink text-white shadow-xs truncate max-w-[65%]">
                    {{ $restaurant->city->name }} • {{ $restaurant->distance }}
                </span>
                @if($restaurant->isOpenNow())
                    <span class="inline-flex items-center gap-1 px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md font-bold bg-open text-white shadow-xs shrink-0">
                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-white"></span>
                        Açık
                    </span>
                @else
                    <span class="px-1.5 sm:px-2.5 py-0.5 sm:py-1 rounded-md font-bold bg-ink text-stone-300 shadow-xs border border-stone-700/40 shrink-0">
                        Kapalı
                    </span>
                @endif
            </div>
        </a>

        <!-- CARD DETAILS -->
        <div class="p-3 sm:p-5 flex flex-col flex-grow">
            <div class="flex items-center justify-between gap-1 mb-1">
                <span class="text-[10px] sm:text-xs font-bold text-muted truncate">{{ $restaurant->cuisine }}</span>
            </div>

            <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="block">
                <h3 class="font-bold sm:font-extrabold text-xs sm:text-base md:text-lg text-ink group-hover:text-terracotta leading-snug line-clamp-1 sm:line-clamp-none">
                    {{ $restaurant->name }}
                </h3>
            </a>

            <p class="text-[11px] sm:text-xs text-muted mt-1 sm:mt-2 line-clamp-2 leading-relaxed font-normal">
                {{ $restaurant->description }}
            </p>

            <!-- Card Bottom Action Link -->
            <div class="mt-auto pt-2.5 sm:pt-4 border-t border-warm flex items-center justify-between gap-1.5 sm:gap-3">
                <span class="text-[9px] sm:text-[11px] font-semibold text-muted truncate">
                    {{ $restaurant->reviews_count }} <span class="hidden sm:inline">değerlendirme</span><span class="sm:hidden">yorum</span>
                </span>
                <a href="{{ route('restaurant.menu', $restaurant->slug) }}"
                   class="inline-flex items-center gap-1 text-[10px] sm:text-xs font-bold text-terracotta hover:text-terracotta-dark shrink-0">
                    <x-ico name="book-open" class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                    <span class="hidden sm:inline">Dijital Menüyü Gör</span>
                    <span class="sm:hidden">Menü</span>
                </a>
            </div>
        </div>
    </div>
@endif
