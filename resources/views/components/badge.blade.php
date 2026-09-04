@props([
    'variant' => 'default'
])

@php
$classes = match($variant) {
    'open' => 'bg-open text-white font-bold',
    'closed' => 'bg-stone-800 text-stone-300 font-bold',
    'rating' => 'bg-ink text-white font-extrabold',
    'chef' => 'bg-amber-50 text-star font-bold border border-amber-200/60',
    'popular' => 'bg-orange-50 text-terracotta font-bold border border-orange-200/60',
    'vegetarian' => 'bg-emerald-50 text-open font-bold border border-emerald-200/60',
    'terracotta' => 'bg-terracotta text-white font-bold',
    'sand' => 'bg-sand text-ink font-semibold border border-warm',
    default => 'bg-surface text-ink font-bold border border-warm',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs shadow-2xs {$classes}"]) }}>
    {{ $slot }}
</span>
