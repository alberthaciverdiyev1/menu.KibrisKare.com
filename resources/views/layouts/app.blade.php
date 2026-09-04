<!DOCTYPE html>
<html lang="tr" class="scroll-smooth bg-sand">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AdaMenü — Kıbrıs Restoran ve Dijital Menü Platformu')</title>
    
    <!-- Favicon -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><circle cx=%2250%22 cy=%2250%22 r=%2245%22 fill=%22%23E85D3F%22/><text x=%2250%22 y=%2260%22 font-size=%2240%22 text-anchor=%22middle%22 fill=%22white%22 font-family=%22sans-serif%22 font-weight=%22bold%22>M</text></svg>">
    
    <!-- Fonts (Full Latin-Ext / Azerbaijani & Turkish Character Support: ə, ı, ğ, ç, ş, ö, ü) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-sand text-ink antialiased min-h-screen flex flex-col selection:bg-terracotta selection:text-white font-sans">

    <!-- Skip link (keyboard accessibility) -->
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-ink focus:text-white focus:px-4 focus:py-2.5 focus:rounded-lg focus:text-sm focus:font-bold focus:shadow-lg">
        İçeriğe Geç
    </a>

    <x-navbar :cities="$cities ?? []" :selectedCity="$selectedCity ?? null" :currentCity="$currentCity ?? null" />

    <!-- MAIN CONTENT -->
    <main id="main-content" class="flex-grow focus:outline-none" tabindex="-1">
        @yield('content')
    </main>

    <x-footer />
</body>
</html>
