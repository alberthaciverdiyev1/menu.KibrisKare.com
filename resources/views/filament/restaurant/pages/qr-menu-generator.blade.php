<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Header Info Card -->
        <div class="relative overflow-hidden rounded-3xl border border-stone-200 dark:border-gray-800 bg-white dark:bg-gray-900 p-6 sm:p-8 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 relative z-10">
                <div class="space-y-2 max-w-2xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-[#E85D3F]/10 text-[#E85D3F]">
                        <span>✦ Tasarım Masaya Hazır QR Kartları</span>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                        Şubelere Özel Premium QR Menüler
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        Sıradan siyah-beyaz barkodlar yerine; restoranınızın logosunu, şube adını ve şık Akdeniz terracotta tonlarını taşıyan masaya hazır QR stant kartları. Yazdırıp masalarınıza, kapınıza veya akrilik ayaklıklara hemen yerleştirebilirsiniz.
                    </p>
                </div>
            </div>
        </div>

        @if(!$restaurant)
            <div class="p-8 text-center bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200">
                Lütfen önce restoran profilinizi kaydedin.
            </div>
        @elseif($branches->isEmpty())
            <div class="p-12 text-center bg-white dark:bg-gray-900 rounded-3xl border border-stone-200 dark:border-gray-800 space-y-4 shadow-xs">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-stone-100 dark:bg-gray-800 flex items-center justify-center text-3xl">
                    🏢
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Henüz Şube Eklenmemiş</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Şubelere özel QR menüler üretebilmek için önce Şubeler (Branches) sekmesinden en az bir şube eklemelisiniz.
                </p>
                <div class="pt-2">
                    <a href="{{ route('filament.restaurant.resources.branches.create') }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#E85D3F] hover:bg-[#d04e32] text-white font-bold text-sm shadow-xs transition-colors">
                        + Yeni Şube Ekle
                    </a>
                </div>
            </div>
        @else
            <!-- Branches QR Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                @foreach($branches as $branch)
                    @php
                        $menuUrl = route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]);
                        // High resolution QR code rendered with terracotta dark color (4A1E17 / 2C1810) on warm white background
                        $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=450x450&margin=10&color=2C1810&bgcolor=FFFFFF&data=" . urlencode($menuUrl);
                        $cardElementId = "qr-stand-card-" . $branch->id;
                    @endphp

                    <div class="flex flex-col justify-between space-y-4">
                        
                        <!-- PREMIUM TABLE STAND CARD (Ready for Print & Capture) -->
                        <div id="{{ $cardElementId }}" 
                             class="relative rounded-3xl p-6 sm:p-7 border border-stone-200/90 dark:border-stone-800 bg-[#FAF7F2] dark:bg-stone-900 text-stone-900 dark:text-stone-100 shadow-md overflow-hidden transition-transform duration-300 hover:-translate-y-1">
                            
                            <!-- Top Decorative Accent -->
                            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-[#E85D3F] via-[#F4A261] to-[#E85D3F]"></div>

                            <!-- Card Header: Restaurant & Branch Branding -->
                            <div class="text-center space-y-2 pt-2">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white dark:bg-stone-800 shadow-xs border border-stone-200/80 dark:border-stone-700 mx-auto overflow-hidden">
                                    @if($restaurant->image)
                                        <img src="{{ $restaurant->image }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xl">🍽️</span>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="text-lg font-black tracking-tight text-[#2C1810] dark:text-white uppercase">
                                        {{ $restaurant->name }}
                                    </h3>
                                    <div class="flex items-center justify-center gap-1.5 mt-0.5">
                                        <span class="text-xs font-bold text-[#E85D3F]">
                                            📍 {{ $branch->name }}
                                        </span>
                                        @if($branch->is_main)
                                            <span class="text-[9px] font-extrabold uppercase px-1.5 py-0.2 rounded bg-amber-200/80 text-amber-900 dark:bg-amber-900/60 dark:text-amber-200">Merkez</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-stone-500 dark:text-stone-400">
                                        {{ $branch->city->name ?? 'Kıbrıs' }} • {{ $restaurant->cuisine }}
                                    </p>
                                </div>
                            </div>

                            <!-- Styled QR Graphic Frame -->
                            <div class="my-5 flex flex-col items-center justify-center">
                                <div class="relative p-3.5 rounded-2xl bg-white shadow-sm border border-stone-200/80 max-w-[210px] w-full aspect-square flex items-center justify-center">
                                    
                                    <!-- QR Image -->
                                    <img src="{{ $qrCodeUrl }}" 
                                         alt="{{ $branch->name }} QR Menü" 
                                         class="w-full h-full object-contain rounded-lg">

                                    <!-- Center Brand Logo Watermark Badge -->
                                    <div class="absolute inset-0 m-auto w-10 h-10 rounded-full bg-white border-2 border-[#E85D3F] shadow-md flex items-center justify-center p-0.5">
                                        <div class="w-full h-full rounded-full bg-[#E85D3F] flex items-center justify-center text-white text-[10px] font-black tracking-tighter">
                                            MENÜ
                                        </div>
                                    </div>
                                </div>

                                <!-- Scan instructions CTA -->
                                <div class="mt-3 text-center space-y-0.5">
                                    <span class="inline-flex items-center gap-1 text-[11px] font-extrabold uppercase tracking-wider text-[#E85D3F]">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span>Kameranızla Okutun</span>
                                    </span>
                                    <p class="text-[10px] text-stone-500 dark:text-stone-400">
                                        Temassız Dijital Menü & Fiyat Listesi
                                    </p>
                                </div>
                            </div>

                            <!-- Card Footer: Fast Facts -->
                            <div class="pt-3 border-t border-stone-200/60 dark:border-stone-800 flex items-center justify-between text-[10px] text-stone-600 dark:text-stone-400">
                                <span class="font-semibold">★ {{ number_format($branch->average_rating, 1) }} ({{ $branch->reviews_count }} Yorum)</span>
                                <span class="font-mono">{{ $branch->getTodayHours() }}</span>
                            </div>

                        </div>

                        <!-- Card Action Buttons -->
                        <div class="grid grid-cols-2 gap-2.5">
                            <a href="{{ $menuUrl }}" 
                               target="_blank" 
                               class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-white hover:bg-stone-100 dark:bg-gray-800 dark:hover:bg-gray-700 border border-stone-200 dark:border-gray-700 transition-colors shadow-2xs">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                <span>Menüyü Test Et</span>
                            </a>

                            <button type="button" 
                                    onclick="printQrCard('{{ $cardElementId }}', '{{ addslashes($restaurant->name) }} - {{ addslashes($branch->name) }}')"
                                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl text-xs font-bold text-white shadow-2xs hover:opacity-95 transition-all" 
                                    style="background-color: #E85D3F;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span>Yazdır / İndir</span>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>

    <!-- Print Helper Script -->
    <script>
        function printQrCard(elementId, title) {
            const el = document.getElementById(elementId);
            if (!el) return;

            const printWindow = window.open('', '_blank', 'width=700,height=800');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>${title} QR Menü</title>
                    <script src="https://cdn.tailwindcss.com"></` + `script>
                    <style>
                        @media print {
                            body { margin: 0; padding: 20px; display: flex; align-items: center; justify-content: center; background: white; }
                            .print-card { width: 340px !important; box-shadow: none !important; border: 1.5px solid #e7e5e4 !important; }
                        }
                        body { background: #f5f5f4; display: flex; align-items: center; justify-content: center; min-height: 100vh; margin: 0; font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
                        .print-card { width: 340px; margin: auto; }
                    </style>
                </head>
                <body>
                    <div class="print-card">
                        ${el.outerHTML}
                    </div>
                    <script>
                        setTimeout(() => {
                            window.print();
                        }, 500);
                    </` + `script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
    </script>
</x-filament-panels::page>
