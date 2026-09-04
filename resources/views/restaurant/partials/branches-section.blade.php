<!-- ================= ŞUBELER & LOKASYONLAR BÖLÜMÜ ================= -->
@if($restaurant->branches->isNotEmpty())
    <section id="subeler" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-5">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                    <x-ico name="map-pin" class="w-5 h-5 text-terracotta" />
                    <span>{{ $restaurant->name }} Şubeleri ({{ $restaurant->branches->count() }})</span>
                </h2>
                <p class="text-xs text-muted mt-0.5">Tüm şubelerin adres, iletişim ve güncel çalışma saatleri</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($restaurant->branches as $branch)
                @php
                    $bIsOpen = $branch->isOpenNow();
                    $bHours = $branch->getTodayHours();
                    $bMapUrl = "https://www.google.com/maps/dir/?api=1&destination={$branch->latitude},{$branch->longitude}";
                @endphp
                <div class="p-4 rounded-xl bg-sand/60 space-y-3 flex flex-col justify-between hover:bg-stone-200/40 transition-colors">
                    <div class="space-y-2">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <h3 class="font-bold text-sm text-ink flex items-center gap-1.5 flex-wrap">
                                    <span>{{ $branch->name }}</span>
                                </h3>
                                @if($branch->city)
                                    <span class="text-[11px] font-medium text-muted">{{ $branch->city->name }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md shrink-0 {{ $bIsOpen ? 'bg-open/10 text-open' : 'bg-rose-50 text-rose-700' }}">
                                {{ $bIsOpen ? 'Açık' : 'Kapalı' }}
                            </span>
                        </div>

                        @if($branch->address)
                            <div class="flex items-start gap-2 text-xs text-muted">
                                <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta/70 shrink-0 mt-0.5" />
                                <span class="line-clamp-2 text-ink/80">{{ $branch->address }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2 text-xs text-muted">
                            <x-ico name="clock" class="w-3.5 h-3.5 text-muted/80 shrink-0" />
                            <span>{{ $bHours }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-stone-200/50 flex items-center gap-2">
                        <a href="{{ route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-terracotta hover:bg-terracotta-dark text-white text-xs font-bold transition-colors">
                            <x-ico name="book-open" class="w-3.5 h-3.5" />
                            <span>Menü</span>
                        </a>

                        @if($branch->phone)
                            <a href="tel:{{ $branch->phone }}"
                               class="inline-flex items-center justify-center gap-1.5 py-2 px-2.5 rounded-lg bg-surface hover:bg-sand text-ink border border-stone-200/60 text-xs font-bold transition-colors">
                                <x-ico name="phone" class="w-3.5 h-3.5 text-terracotta" />
                                <span>Ara</span>
                            </a>
                        @endif

                        @if($branch->latitude && $branch->longitude)
                            <a href="{{ $bMapUrl }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center p-2 rounded-lg bg-surface hover:bg-sand text-terracotta border border-stone-200/60 transition-colors"
                               title="Yol Tarifi">
                                <x-ico name="navigation" class="w-3.5 h-3.5" />
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
