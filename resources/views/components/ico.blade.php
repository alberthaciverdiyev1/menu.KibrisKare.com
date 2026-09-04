@props(['name' => 'circle', 'filled' => false])

@php
    // Curated inline SVG icon set (Heroicons-style outline; star uses a filled glyph).
    // Stroke icons share one visual language (24px viewBox, stroke-width 2) so the UI
    // reads as one icon family instead of mixed emoji / glyph characters.
    $icons = [
        'star' => [
            'filled' => true,
            'paths' => [
                'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L3.127 10.1c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z',
            ],
        ],
        'phone' => [
            'paths' => [
                'M3 5a2 2 0 012-2h.5a1 1 0 01.94.66l1.27 3.25a1 1 0 01-.34 1.16l-1.2.9a15.03 15.03 0 006.55 6.55l.9-1.2a1 1 0 011.16-.34l3.25 1.27a1 1 0 01.66.94V19a2 2 0 01-2 2H16.5C9.11 21 3 14.89 3 7.5V5z',
            ],
        ],
        'map-pin' => [
            'paths' => [
                'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z',
                'M15 11a3 3 0 11-6 0 3 3 0 016 0z',
            ],
        ],
        'map' => [
            'paths' => [
                'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7',
            ],
        ],
        'book-open' => [
            'paths' => [
                'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            ],
        ],
        'tag' => [
            'paths' => [
                'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z',
            ],
        ],
        'external' => [
            'paths' => [
                'M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14',
            ],
        ],
        'close' => [
            'paths' => [
                'M6 18L18 6M6 6l12 12',
            ],
        ],
        'check' => [
            'paths' => [
                'M5 13l4 4L19 7',
            ],
        ],
        'chevron-right' => [
            'paths' => [
                'M9 5l7 7-7 7',
            ],
        ],
        'chevron-down' => [
            'paths' => [
                'M19 9l-7 7-7-7',
            ],
        ],
        'search' => [
            'paths' => [
                'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
            ],
        ],
    ];

    $icon = $icons[$name] ?? ['paths' => ['M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z']];
    $useFilled = $filled || ($icon['filled'] ?? false);
@endphp

<svg {{ $attributes->merge([
    'class' => 'w-5 h-5',
    'viewBox' => '0 0 24 24',
    'fill' => $useFilled ? 'currentColor' : 'none',
    'stroke' => $useFilled ? 'none' : 'currentColor',
    'stroke-width' => $useFilled ? '0' : '2',
    'aria-hidden' => 'true',
]) }}>
    @foreach($icon['paths'] as $path)
        <path d="{{ $path }}" @if(!$useFilled) stroke-linecap="round" stroke-linejoin="round" @endif />
    @endforeach
</svg>
