@props([
    'eyebrow' => null,
    'title',
    'subtitle' => null,
    'actionText' => null,
    'actionUrl' => null,
    'badge' => null
])

<div class="flex items-baseline justify-between mb-8 pb-3 border-b border-warm">
    <div>
        @if($eyebrow)
            <span class="text-xs font-bold uppercase tracking-wider text-muted block">{{ $eyebrow }}</span>
        @endif
        <div class="flex items-center gap-2 mt-0.5">
            <h2 class="text-2xl font-black text-ink">
                {{ $title }}
            </h2>
            @if($badge)
                <span class="text-sm font-semibold text-muted">{{ $badge }}</span>
            @endif
        </div>
        @if($subtitle)
            <p class="text-sm text-muted mt-1 font-medium">{{ $subtitle }}</p>
        @endif
    </div>

    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="text-xs font-bold text-terracotta hover:text-terracotta-dark uppercase tracking-wider flex items-center gap-1 shrink-0">
            <span>{{ $actionText }}</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
        </a>
    @endif
</div>
