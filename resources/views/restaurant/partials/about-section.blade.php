<!-- ================= MEKAN HAKKINDA ================= -->
<section class="bg-surface rounded-2xl p-6 shadow-2xs space-y-3">
    <h2 class="text-lg font-bold text-ink">Mekan Hakkında</h2>
    <p class="text-xs sm:text-sm text-ink/80 leading-relaxed font-normal">
        {{ $restaurant->description ?: 'Misafirlerimize özenle hazırlanan lezzetler ve kaliteli bir atmosfer sunuyoruz.' }}
    </p>
    <div class="pt-2 flex flex-wrap gap-2">
        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sand text-xs font-semibold text-ink">
            Mutfak: {{ $restaurant->cuisine }}
        </span>
        <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sand text-xs font-semibold text-ink">
            Şehir: {{ $restaurant->city->name }}
        </span>
        @if($restaurant->price_range)
            <span class="inline-flex items-center px-3 py-1.5 rounded-lg bg-sand text-xs font-bold text-terracotta font-mono">
                Fiyat: {{ $restaurant->price_range }}
            </span>
        @endif
    </div>
</section>
