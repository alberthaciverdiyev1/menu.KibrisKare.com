<!-- ================= İLETİŞİM KARTI ================= -->
<div id="sidebar-iletisim" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-5">
    <h3 class="text-base font-bold text-ink">İletişim</h3>

    <div class="space-y-4">
        <!-- Adres -->
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-full bg-sand text-terracotta flex items-center justify-center shrink-0 mt-0.5">
                <x-ico name="map-pin" class="w-5 h-5 text-terracotta" />
            </div>
            <div class="min-w-0">
                <p class="text-xs sm:text-sm font-semibold text-ink leading-snug">
                    {{ $address ?: 'Hurmalı, 34006. Sk. No:6, 01060 Seyhan/Adana' }}
                </p>
                <p class="text-[11px] text-muted mt-0.5">
                    {{ $restaurant->city->name }}
                </p>
            </div>
        </div>

        <!-- Telefon -->
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-sand text-terracotta flex items-center justify-center shrink-0">
                <x-ico name="phone" class="w-5 h-5 text-terracotta" />
            </div>
            <a href="tel:{{ $restaurant->phone ?: '03224367666' }}" class="text-xs sm:text-sm font-semibold text-ink hover:text-terracotta transition-colors">
                {{ $restaurant->phone ?: '(0322) 436 76 66' }}
            </a>
        </div>

        <!-- Web Sitesi -->
        <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-sand text-terracotta flex items-center justify-center shrink-0">
                <x-ico name="globe" class="w-5 h-5 text-terracotta" />
            </div>
            <a href="{{ $restaurant->website ?: ('http://' . $restaurant->slug . '.com/') }}" target="_blank" rel="noopener noreferrer" class="text-xs sm:text-sm font-medium text-terracotta hover:text-terracotta-dark hover:underline truncate transition-colors">
                {{ $restaurant->website ?: ('http://' . $restaurant->slug . '.com/') }}
            </a>
        </div>
    </div>

    <div class="border-t border-stone-100 my-4"></div>

    <!-- Action Buttons (Ara & Yol Tarifi) -->
    <div class="grid grid-cols-2 gap-2.5">
        <!-- Ara -->
        <a href="tel:{{ $restaurant->phone ?: '03224367666' }}"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-white border border-stone-200/80 hover:bg-stone-50 text-ink text-xs sm:text-sm font-bold shadow-2xs transition-colors">
            <x-ico name="phone" class="w-4 h-4 text-ink" />
            <span>Ara</span>
        </a>

        <!-- Yol Tarifi -->
        <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer"
           class="flex items-center justify-center gap-2 py-3 px-3 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white text-xs sm:text-sm font-bold shadow-2xs transition-colors">
            <x-ico name="navigation" class="w-4 h-4" />
            <span>Yol Tarifi</span>
        </a>
    </div>
</div>
