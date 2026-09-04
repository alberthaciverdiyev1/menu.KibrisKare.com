@if($restaurant->branches->isNotEmpty())
    <section id="subeler" class="bg-surface rounded-2xl p-6 shadow-2xs space-y-4"
             x-data="{ open: window.innerWidth >= 640 }"
             @resize.window="if (window.innerWidth >= 640) open = true">
        
        <!-- Header with Mobile Collapse Toggle -->
        <div class="flex items-center justify-between select-none cursor-pointer sm:cursor-default"
             @click="if (window.innerWidth < 640) open = !open">
            <div>
                <h2 class="text-lg font-bold text-ink flex items-center gap-2">
                    <x-ico name="map-pin" class="w-5 h-5 text-terracotta" />
                    <span>{{ $restaurant->name }} Şubeleri ({{ $restaurant->branches->count() }})</span>
                </h2>
                <p class="text-xs text-muted mt-0.5">Tüm şubelerin adres, iletişim ve güncel çalışma saatleri</p>
            </div>

            <!-- Mobile Chevron Button -->
            <button type="button"
                    class="sm:hidden p-2 rounded-xl bg-sand text-ink hover:bg-stone-200/60 transition-transform duration-200 cursor-pointer shrink-0"
                    :class="open ? 'rotate-180' : ''"
                    aria-label="Şubeleri Göster / Gizle">
                <x-ico name="chevron-down" class="w-5 h-5 text-ink" />
            </button>
        </div>

        <!-- Branches Grid (2 cards per row on mobile & desktop) -->
        <div x-show="open"
             x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="grid grid-cols-2 gap-2.5 sm:gap-4 pt-1 sm:!grid">
            @foreach($restaurant->branches as $branch)
                @php
                    $bIsOpen = $branch->isOpenNow();
                    $bHours = $branch->getTodayHours();
                    $bMapUrl = "https://www.google.com/maps/dir/?api=1&destination={$branch->latitude},{$branch->longitude}";
                @endphp
                <div class="p-3 sm:p-4 rounded-xl bg-sand/60 space-y-2.5 sm:space-y-3 flex flex-col justify-between hover:bg-stone-200/40 transition-colors">
                    <div class="space-y-1.5 sm:space-y-2">
                        <div class="flex items-start justify-between gap-1.5">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold text-xs sm:text-sm text-ink truncate" title="{{ $branch->name }}">
                                    {{ $branch->name }}
                                </h3>
                                @if($branch->city)
                                    <span class="text-[10px] sm:text-[11px] font-medium text-muted block truncate">{{ $branch->city->name }}</span>
                                @endif
                            </div>
                            <span class="text-[9px] sm:text-[10px] font-extrabold px-1.5 py-0.5 rounded shrink-0 {{ $bIsOpen ? 'bg-open/10 text-open' : 'bg-rose-50 text-rose-700' }}">
                                {{ $bIsOpen ? 'Açık' : 'Kapalı' }}
                            </span>
                        </div>

                        @if($branch->address)
                            <div class="flex items-start gap-1.5 text-[11px] sm:text-xs text-muted">
                                <x-ico name="map-pin" class="w-3.5 h-3.5 text-terracotta/70 shrink-0 mt-0.5" />
                                <span class="line-clamp-2 text-ink/80 leading-snug">{{ $branch->address }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-1.5 text-[11px] sm:text-xs text-muted">
                            <x-ico name="clock" class="w-3.5 h-3.5 text-muted/80 shrink-0" />
                            <span class="truncate">{{ $bHours }}</span>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-stone-200/50 flex items-center gap-1.5 sm:gap-2">
                        <a href="{{ route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]) }}"
                           class="flex-1 inline-flex items-center justify-center gap-1 py-1.5 sm:py-2 px-1.5 sm:px-2.5 rounded-lg bg-terracotta hover:bg-terracotta-dark text-white text-[11px] sm:text-xs font-bold transition-colors">
                            <x-ico name="book-open" class="w-3 h-3 sm:w-3.5 sm:h-3.5 shrink-0" />
                            <span>Menü</span>
                        </a>

                        @if($branch->phone)
                            <a href="tel:{{ $branch->phone }}"
                               class="inline-flex items-center justify-center gap-1 py-1.5 sm:py-2 px-1.5 sm:px-2.5 rounded-lg bg-surface hover:bg-sand text-ink border border-stone-200/60 text-[11px] sm:text-xs font-bold transition-colors"
                               title="Ara">
                                <x-ico name="phone" class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-terracotta shrink-0" />
                                <span class="hidden sm:inline">Ara</span>
                            </a>
                        @endif

                        @if($branch->latitude && $branch->longitude)
                            <a href="{{ $bMapUrl }}" target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center justify-center p-1.5 sm:p-2 rounded-lg bg-surface hover:bg-sand text-terracotta border border-stone-200/60 transition-colors shrink-0"
                               title="Yol Tarifi">
                                <x-ico name="navigation" class="w-3 h-3 sm:w-3.5 sm:h-3.5" />
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
