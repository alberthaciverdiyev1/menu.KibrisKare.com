<!-- FOOTER (Neutral Charcoal with Warm Terracotta Accent) -->
<footer class="bg-ink text-muted border-t border-stone-800 pt-16 pb-12 mt-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-stone-800">
            
            <div class="md:col-span-5 space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-terracotta text-white flex items-center justify-center font-black text-sm">
                        M
                    </div>
                    <span class="font-extrabold text-2xl tracking-tight text-white uppercase">
                        ADA<span class="text-terracotta">MENÜ</span>
                    </span>
                </div>
                <p class="text-sm text-stone-400 leading-relaxed max-w-sm font-normal">
                    Kuzey Kıbrıs genelindeki restoranların, meyhanelerin ve kafelerin doğrulanmış dijital menü ve fiyat rehberi.
                </p>
                <div class="text-xs font-semibold text-stone-500">
                    Girne • Lefkoşa • Gazimağusa • İskele • Güzelyurt • Lefke
                </div>
            </div>

            <div class="md:col-span-2 space-y-3">
                <div class="text-xs font-bold text-white uppercase tracking-wider">Hızlı Erişim</div>
                <ul class="space-y-2.5 text-sm text-stone-400 font-medium">
                    <li><a href="{{ route('restaurants.index') }}" class="hover:text-white">Tüm Restoranlar</a></li>
                    <li><a href="{{ route('map') }}" class="hover:text-white">Kıbrıs Haritası</a></li>
                    <li><a href="{{ route('categories') }}" class="hover:text-white">Mutfak Türleri</a></li>
                    <li><a href="{{ route('restaurants.index', ['city' => 'girne']) }}" class="hover:text-white">Girne Restoranları</a></li>
                </ul>
            </div>

            <div class="md:col-span-2 space-y-3">
                <div class="text-xs font-bold text-white uppercase tracking-wider">Popüler Mutfaklar</div>
                <ul class="space-y-2.5 text-sm text-stone-400 font-medium">
                    <li><a href="{{ route('restaurants.index', ['category' => 'kebap']) }}" class="hover:text-white">Kıbrıs Şeftali Kebabı</a></li>
                    <li><a href="{{ route('restaurants.index', ['category' => 'deniz-urunleri']) }}" class="hover:text-white">Deniz Ürünleri & Balık</a></li>
                    <li><a href="{{ route('restaurants.index', ['category' => 'pizza']) }}" class="hover:text-white">Taş Fırın Pizza</a></li>
                    <li><a href="{{ route('restaurants.index', ['category' => 'burger']) }}" class="hover:text-white">Gurme Burger</a></li>
                </ul>
            </div>

            <div class="md:col-span-3 space-y-3">
                <div class="text-xs font-bold text-white uppercase tracking-wider">Restoran Sahipleri</div>
                <p class="text-xs text-stone-400 leading-relaxed font-normal">
                    Mekanınızı listemize ekleyin, misafirlerinize güncel QR ve web menünüzü sunun.
                </p>
                <div class="flex flex-col gap-2 pt-1">
                    <a href="/restaurant-panel" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold shadow-xs text-center">
                        <span>Restoran Portalı Girişi →</span>
                    </a>
                    <a href="/admin" class="text-[11px] text-stone-500 hover:text-stone-400 text-center">
                        Sistem Yöneticisi (Admin)
                    </a>
                </div>
            </div>

        </div>

        <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
            <p>© {{ date('Y') }} AdaMenü Kıbrıs. Tüm hakları saklıdır.</p>
            <div class="flex items-center gap-6 font-medium">
                <a href="{{ route('home') }}" class="hover:text-white">Ana Sayfa</a>
                <a href="{{ route('restaurants.index') }}" class="hover:text-white">Restoranlar</a>
                <a href="{{ route('map') }}" class="hover:text-white">Harita</a>
                <a href="/admin" class="hover:text-white">Panel</a>
            </div>
        </div>
    </div>
</footer>
