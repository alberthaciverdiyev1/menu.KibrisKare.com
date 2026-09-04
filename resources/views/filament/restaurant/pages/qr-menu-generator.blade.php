<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Info Card -->
        <div class="bg-white dark:bg-gray-900 border border-stone-200 dark:border-gray-800 rounded-2xl p-6 shadow-xs">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="text-2xl">📱</span>
                        <span>Şube Masaları İçin Özel QR Menüler</span>
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Her şubenizin masalarına, giriş kapısına veya broşürlerine yerleştirebileceğiniz özel QR kodları aşağıda listelenmiştir. Müşteriler bu kodu okuttuklarında doğrudan o şubenin güncel menüsüne ve çalışma saatlerine ulaşırlar.
                    </p>
                </div>
            </div>
        </div>

        @if(!$restaurant)
            <div class="p-8 text-center bg-amber-50 dark:bg-amber-950/30 rounded-2xl border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-200">
                Lütfen önce restoran profilinizi kaydedin.
            </div>
        @elseif($branches->isEmpty())
            <div class="p-12 text-center bg-white dark:bg-gray-900 rounded-2xl border border-stone-200 dark:border-gray-800 space-y-4">
                <div class="w-16 h-16 mx-auto rounded-full bg-stone-100 dark:bg-gray-800 flex items-center justify-center text-3xl">
                    🏢
                </div>
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">Henüz Şube Eklenmemiş</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                    Şubelere özel QR menüler üretebilmek için önce Şubeler (Branches) sekmesinden en az bir şube eklemelisiniz.
                </p>
                <div class="pt-2">
                    <a href="{{ route('filament.restaurant.resources.branches.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-sm shadow-xs transition-colors" style="background-color: #E85D3F;">
                        + Yeni Şube Ekle
                    </a>
                </div>
            </div>
        @else
            <!-- Branches QR Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($branches as $branch)
                    @php
                        $menuUrl = route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]);
                        $qrApiUrl = "https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=12&data=" . urlencode($menuUrl);
                    @endphp

                    <div class="bg-white dark:bg-gray-900 border border-stone-200 dark:border-gray-800 rounded-2xl p-6 shadow-xs flex flex-col justify-between space-y-6 hover:border-terracotta/40 transition-all">
                        
                        <!-- Header -->
                        <div>
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-lg font-extrabold text-gray-900 dark:text-white">
                                            {{ $branch->name }}
                                        </h3>
                                        @if($branch->is_main)
                                            <span class="text-[10px] uppercase tracking-wider font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-300">
                                                Merkez
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                        <span>📍 {{ $branch->city->name ?? 'Kıbrıs' }}</span>
                                        @if($branch->address)
                                            <span>• {{ Str::limit($branch->address, 35) }}</span>
                                        @endif
                                    </p>
                                </div>

                                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-semibold {{ $branch->is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400' }}">
                                    {{ $branch->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </div>

                            <!-- QR Code Preview Card -->
                            <div class="mt-6 flex flex-col items-center justify-center p-6 bg-stone-50 dark:bg-gray-800/60 rounded-xl border border-stone-200/80 dark:border-gray-700/80">
                                <div class="bg-white p-3 rounded-xl shadow-xs border border-stone-200">
                                    <img src="{{ $qrApiUrl }}" alt="{{ $branch->name }} QR Menü" class="w-48 h-48 object-contain rounded-lg">
                                </div>
                                <span class="text-[11px] font-semibold text-gray-500 dark:text-gray-400 mt-3 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-terracotta" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    Kameranızla okutup test edebilirsiniz
                                </span>
                            </div>

                            <!-- Menu Link details -->
                            <div class="mt-4 p-2.5 rounded-lg bg-stone-100/70 dark:bg-gray-800 text-[11px] font-mono text-gray-600 dark:text-gray-300 truncate">
                                <a href="{{ $menuUrl }}" target="_blank" class="hover:underline flex items-center justify-between gap-2">
                                    <span class="truncate">{{ $menuUrl }}</span>
                                    <svg class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-stone-100 dark:border-gray-800">
                            <a href="{{ $menuUrl }}" 
                               target="_blank" 
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-200 bg-stone-100 hover:bg-stone-200 dark:bg-gray-800 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <span>Menüyü Gör</span>
                            </a>

                            <a href="{{ $qrApiUrl }}" 
                               download="{{ Str::slug($restaurant->name . '-' . $branch->name) }}-qr.png" 
                               target="_blank" 
                               class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white shadow-2xs hover:opacity-90 transition-opacity" style="background-color: #E85D3F;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                <span>QR İndir</span>
                            </a>
                        </div>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-filament-panels::page>
