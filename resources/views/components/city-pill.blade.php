@props([
    'active' => false,
    'href' => '#',
    'count' => null
])

<a href="{{ $href }}" 
   {{ $attributes->merge([
       'class' => 'px-3.5 py-1.5 rounded-full text-xs font-bold shrink-0 ' . 
                  ($active ? 'bg-terracotta text-white shadow-xs' : 'bg-surface text-ink border border-warm hover:border-muted')
   ]) }}>
    {{ $slot }}
    @if(!is_null($count))
        <span class="opacity-80 font-normal">({{ $count }})</span>
    @endif
</a>
