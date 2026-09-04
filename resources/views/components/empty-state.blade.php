@props([
    'title' => 'Sonuç Bulunamadı',
    'message' => 'Arama kriterlerinize uygun sonuç bulunamadı.',
    'actionText' => null,
    'actionUrl' => null
])

<div class="bg-surface rounded-2xl border border-warm p-12 text-center my-6 shadow-xs">
    <div class="w-12 h-12 mx-auto rounded-full bg-sand flex items-center justify-center text-muted mb-4">
        <svg class="w-6 h-6 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    </div>
    <h3 class="font-extrabold text-ink text-base">{{ $title }}</h3>
    <p class="text-xs text-muted mt-1 max-w-sm mx-auto leading-relaxed">{{ $message }}</p>
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" class="mt-4 inline-block px-5 py-2.5 rounded-xl bg-terracotta text-white text-xs font-bold hover:bg-terracotta-dark transition-colors shadow-xs">
            {{ $actionText }}
        </a>
    @endif
</div>
