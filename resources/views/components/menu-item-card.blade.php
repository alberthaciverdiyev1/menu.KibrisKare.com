@props([
    'dish',
    'showMenuLink' => false,
    'slug' => null
])

@php
    $image = $dish->image ?: 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80';
@endphp

<div class="bg-surface p-4 sm:p-5 rounded-2xl border border-warm hover:border-muted transition-all duration-200 flex flex-col justify-between shadow-2xs hover:shadow-xs group"
     {{ $attributes }}>
    
    <div class="flex items-start gap-4">
        <!-- Dish Details -->
        <div class="flex-grow min-w-0">
            <!-- Badges -->
            <div class="flex items-center gap-1.5 flex-wrap mb-1.5">
                @if($dish->is_chef_special)
                    <span class="font-bold text-star bg-amber-50 px-2 py-0.5 rounded text-[11px] border border-amber-200/50">
                        ⭐ Şefin Seçimi
                    </span>
                @endif
                @if($dish->is_popular)
                    <span class="font-bold text-terracotta bg-orange-50 px-2 py-0.5 rounded text-[11px] border border-orange-200/50">
                        🔥 Çok Satan
                    </span>
                @endif
                @if($dish->is_vegetarian)
                    <span class="font-bold text-open bg-emerald-50 px-2 py-0.5 rounded text-[11px] border border-emerald-200/50">
                        🌱 Vejetaryen
                    </span>
                @endif
            </div>

            <!-- Title -->
            <h3 class="font-extrabold text-base text-ink group-hover:text-terracotta transition-colors leading-snug">
                {{ $dish->name }}
            </h3>

            <!-- Description -->
            @if($dish->description)
                <p class="text-xs text-muted mt-1.5 line-clamp-2 leading-relaxed font-normal">
                    {{ $dish->description }}
                </p>
            @endif

            <!-- Price -->
            <div class="mt-3">
                <span class="font-black text-base text-ink font-mono tracking-tight">
                    {{ number_format($dish->price, 0) }} {{ $dish->currency }}
                </span>
            </div>
        </div>

        <!-- Food Photo -->
        <div class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-xl overflow-hidden bg-sand shrink-0 shadow-2xs border border-warm">
            <img src="{{ $image }}" 
                 alt="{{ $dish->name }}" 
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        </div>
    </div>

    <!-- Card Footer -->
    <div class="mt-4 pt-2.5 border-t border-warm/60 flex items-center justify-between text-xs">
        <span class="text-muted font-medium text-[11px]">
            Taze Hazırlanır
        </span>

        @if($showMenuLink && $slug)
            <a href="{{ route('restaurant.menu', $slug) }}" class="font-bold text-terracotta hover:text-terracotta-dark transition-colors">
                Menüde Gör →
            </a>
        @endif
    </div>
</div>
