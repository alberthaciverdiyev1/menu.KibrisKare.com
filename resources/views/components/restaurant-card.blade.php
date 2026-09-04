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
                <span class="text-[10px] font-extrabold text-ink shrink-0 font-mono">
                    {{ $restaurant->price_range }}
                </span>
            </div>

            <div class="flex items-center gap-1.5 text-[11px] text-muted mt-0.5">
                <span class="font-bold text-star flex items-center gap-0.5">
                    <x-ico name="star" filled class="w-3 h-3" />
                    <span>{{ number_format($restaurant->rating, 1) }}</span>
                </span>
                <span>•</span>
                <span class="truncate">{{ $restaurant->city->name }}</span>
                <span>•</span>
                @if($restaurant->is_open)
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
    <div class="group bg-surface rounded-2xl border border-warm hover:border-muted overflow-hidden flex flex-col shadow-xs hover:shadow-md">
        
        <!-- IMAGE CONTAINER (No gradient overlay) -->
        <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="relative aspect-[16/10] w-full overflow-hidden bg-sand block">
            <img src="{{ $restaurant->image }}" 
                 alt="{{ $restaurant->name }}" 
                 loading="lazy"
                 class="w-full h-full object-cover">

            <!-- Rating (Top Left) -->
            <div class="absolute top-3 left-3 flex items-center gap-1.5">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-extrabold bg-ink text-white shadow-xs border border-stone-700/40">
                    <x-ico name="star" filled class="w-3 h-3 text-star" />
                    <span>{{ number_format($restaurant->rating, 1) }}</span>
                    <span class="text-stone-300 text-[11px] font-medium">({{ $restaurant->reviews_count }})</span>
                </span>
            </div>

            <!-- Price Range (Top Right) -->
            <div class="absolute top-3 right-3">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-surface text-ink shadow-xs border border-warm">
                    {{ $restaurant->price_range }}
                </span>
            </div>

            <!-- Location & Status (Bottom Solid Badges, No Gradient) -->
            <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-xs">
                <span class="px-2.5 py-1 rounded-md font-bold bg-ink text-white shadow-xs">
                    {{ $restaurant->city->name }} • {{ $restaurant->distance }}
                </span>
                @if($restaurant->is_open)
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md font-bold bg-open text-white shadow-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        Açık
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-md font-bold bg-ink text-stone-300 shadow-xs border border-stone-700/40">
                        Kapalı
                    </span>
                @endif
            </div>
        </a>

        <!-- CARD DETAILS -->
        <div class="p-5 flex flex-col flex-grow justify-between bg-surface">
            <div>
                <!-- Cuisine Badge -->
                <div class="text-xs font-bold uppercase tracking-wider text-muted mb-1.5 truncate">
                    {{ $restaurant->cuisine }}
                </div>

                <!-- Restaurant Title -->
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" class="block">
                    <h3 class="font-extrabold text-lg text-ink group-hover:text-terracotta leading-snug">
                        {{ $restaurant->name }}
                    </h3>
                </a>

                <!-- Address / Excerpt -->
                <p class="text-xs text-muted mt-2 line-clamp-2 leading-relaxed font-normal">
                    {{ $restaurant->description }}
                </p>
            </div>

            <!-- Card Footer: Quick Actions -->
            <div class="mt-5 pt-3.5 border-t border-warm flex items-center justify-between gap-3">
                <a href="{{ route('restaurant.show', $restaurant->slug) }}" 
                   class="text-xs font-bold text-muted hover:text-ink">
                    Mekan Bilgisi
                </a>

                <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-bold text-terracotta hover:text-terracotta-dark group/btn">
                    <span>Menüyü Gör</span>
                    <svg class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

        </div>
    </div>
@endif
