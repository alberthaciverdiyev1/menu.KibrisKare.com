@props([
    'active' => false,
    'href' => '#',
    'count' => null,
    'variant' => 'terracotta' // 'terracotta' or 'ink'
])

@php
$activeClass = $variant === 'ink' 
    ? 'bg-ink text-white shadow-xs border-ink' 
    : 'bg-terracotta text-white shadow-xs border-terracotta';
@endphp

<a href="{{ $href }}" 
   {{ $attributes->merge([
       'class' => 'px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 border ' . 
                  ($active ? $activeClass : 'bg-surface text-ink border-warm hover:border-muted')
   ]) }}>
    {{ $slot }}
    @if(!is_null($count))
        <span class="opacity-80 font-normal">({{ $count }})</span>
    @endif
</a>
