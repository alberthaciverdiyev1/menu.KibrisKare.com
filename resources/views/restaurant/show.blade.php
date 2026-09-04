@extends('layouts.app')

@section('title', $restaurant->name . " — Kıbrıs Restoran Rehberi & Menü | AdaMenü")

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-10">

        <!-- BREADCRUMBS (vilka.az style) -->
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-1.5 text-xs text-muted">
            <a href="{{ route('home') }}" class="hover:text-ink transition-colors">Ana Sayfa</a>
            <span class="text-stone-300">›</span>
            <a href="{{ route('restaurants.index') }}" class="hover:text-ink transition-colors">Restoranlar</a>
            <span class="text-stone-300">›</span>
            <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" class="hover:text-ink transition-colors">{{ $restaurant->city->name }}</a>
            <span class="text-stone-300">›</span>
            <span class="text-ink font-semibold">{{ $restaurant->name }}</span>
        </nav>

        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center gap-3 shadow-2xs">
                <span class="text-lg">✅</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- HEADER SECTION (vilka.az clean typography header) -->
        <header class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-ink tracking-tight font-display">
                        {{ $restaurant->name }}
                    </h1>
                    
                    <!-- Metadata subtitle line -->
                    <div class="flex flex-wrap items-center gap-2 text-xs sm:text-sm text-muted mt-2 font-medium">
                        <span class="text-ink font-semibold">Restoran</span>
                        <span>·</span>
                        <span>{{ $restaurant->cuisine }}</span>
                        <span>·</span>
                        <span>{{ $restaurant->city->name }}, Kıbrıs</span>
                        <span>·</span>
                        <span class="text-ink font-bold">{{ $restaurant->price_range ?? '₺₺' }}</span>
                        <span>·</span>
                        <span class="inline-flex items-center gap-1 text-ink font-bold">
                            <span class="text-amber-500">★</span>
                            <span>{{ number_format($restaurant->rating, 1) }}</span>
                            <span class="text-muted font-normal">({{ $restaurant->reviews_count }} yorum)</span>
                        </span>
                    </div>
                </div>

                <!-- Right Action / Status -->
                <div class="shrink-0 flex items-center gap-3">
                    @if($restaurant->isOpenNow())
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Şu Anda Açık</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-stone-100 text-stone-600 border border-stone-200">
                            <span>Şu Anda Kapalı</span>
                        </span>
                    @endif
                </div>
            </div>

            <!-- Address & Phone line -->
            <div class="pt-2 text-xs sm:text-sm text-muted space-y-1">
                <p class="flex items-center gap-2">
                    <span class="font-bold text-ink">Adres:</span>
                    <span>{{ $restaurant->display_address }} ({{ $restaurant->city->name }})</span>
                </p>
                @if($restaurant->phone)
                    <p class="flex items-center gap-2">
                        <span class="font-bold text-ink">Telefon:</span>
                        <a href="tel:{{ $restaurant->phone }}" class="text-terracotta hover:underline font-bold">{{ $restaurant->phone }}</a>
                    </p>
                @endif
            </div>
        </header>

        <!-- PHOTO GALLERY (vilka.az clean 3-col or 2-col image layout) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 rounded-2xl overflow-hidden shadow-xs">
            <div class="sm:col-span-2 overflow-hidden bg-sand aspect-[16/10] sm:aspect-auto sm:h-96 relative group">
                <img src="{{ $restaurant->image }}" 
                     alt="{{ $restaurant->name }}" 
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <div class="absolute bottom-3 left-3">
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-ink/80 text-white backdrop-blur-xs">
                        📸 Ana Mekan Görünümü
                    </span>
                </div>
            </div>
            
            <div class="hidden sm:flex flex-col gap-3 h-96">
                <div class="flex-1 overflow-hidden bg-sand rounded-xl relative group">
                    <img src="{{ $restaurant->cover_image ?? $restaurant->image }}" 
                         alt="{{ $restaurant->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute bottom-2 left-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-ink/70 text-white backdrop-blur-xs">
                            Ambiyans & Masa Düzeni
                        </span>
                    </div>
                </div>
                <!-- Action Box on the right side of gallery -->
                <div class="bg-surface border border-warm rounded-xl p-4 flex flex-col justify-between shadow-2xs">
                    <div>
                        <span class="text-xs font-bold text-terracotta uppercase tracking-wider block">Temassız Dijital Menü</span>
                        <h4 class="font-bold text-sm text-ink mt-0.5">Menüyü Telefonunuzda Görün</h4>
                        <p class="text-[11px] text-muted mt-1">Güncel yemek çeşitleri ve fiyat listesi</p>
                    </div>
                    <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                       class="mt-3 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-xs text-center transition-colors">
                        <span>📖 Menüyü ve Fiyatları Gör</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN CONTENT COLUMNS -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- LEFT 8 COLS: ABOUT, AMENITIES, BRANCHES, REVIEWS -->
            <div class="lg:col-span-8 space-y-12">

                <!-- 1. ABOUT SECTION (vilka.az haqqinda section) -->
                <section class="space-y-3">
                    <h2 class="text-2xl font-bold text-ink font-display">
                        {{ $restaurant->name }} Hakkında
                    </h2>
                    <p class="text-sm sm:text-base text-muted leading-relaxed font-normal">
                        {{ $restaurant->description ?? 'Kıbrıs\'ın seçkin lezzetlerini ve kaliteli restoran deneyimini misafirlerine sunan özenli mutfak.' }}
                    </p>
                    
                    <div class="p-4 rounded-2xl bg-surface border border-warm flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4 shadow-2xs">
                        <div class="text-xs">
                            <span class="font-bold text-ink block">📱 QR Menü ve Sipariş Danışma</span>
                            <span class="text-muted">Masa stantlarından veya doğrudan menü sayfamızdan tüm yemekleri inceleyin.</span>
                        </div>
                        <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl bg-ink hover:bg-terracotta text-white font-bold text-xs shrink-0 transition-colors">
                            <span>Dijital Menü</span>
                            <span>→</span>
                        </a>
                    </div>
                </section>

                <!-- 2. AMENITIES & FACILITIES (vilka.az İmkanlar və şərait grid) -->
                <section class="space-y-4 pt-8 border-t border-warm">
                    <h2 class="text-2xl font-bold text-ink font-display">
                        İmkanlar ve Şartlar
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6 pt-2">
                        
                        <!-- Otopark / Ulaşım -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-muted">Park Yeri & Ulaşım</h3>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Özel Otopark Alanı Mevcut</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Kolay Cadde Girişi</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Salon & Ambiyans -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-muted">Salon ve Ortam</h3>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Açık Hava / Teras & Bahçe</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Klimalı Kapalı Salon</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Özel Aile / Grup Bölümü</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Hizmetler -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-muted">Hizmetler & Ödeme</h3>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Ücretsiz Yüksek Hızlı Wi-Fi</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Kredi Kartı / Temassız Ödeme</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Paket Servis & Gel-Al</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Erişilebilirlik & Konfor -->
                        <div class="space-y-2">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-muted">Erişilebilirlik</h3>
                            <ul class="space-y-1.5 text-xs sm:text-sm text-ink">
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Bebek Arabası & Engelsiz Giriş</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <span class="text-emerald-600 font-bold">✓</span>
                                    <span>Mama Sandalyesi Mevcut</span>
                                </li>
                            </ul>
                        </div>

                    </div>
                </section>

                <!-- 3. FEATURED MENU DISHES (İmza Lezzetler) -->
                @if(isset($featuredItems) && $featuredItems->isNotEmpty())
                    <section class="space-y-4 pt-8 border-t border-warm">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-ink font-display">
                                Öne Çıkan Lezzetler
                            </h2>
                            <a href="{{ route('restaurant.menu', $restaurant->slug) }}" 
                               class="text-xs font-bold text-terracotta hover:underline">
                                Tüm Menüyü Gör →
                            </a>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            @foreach($featuredItems as $dish)
                                <x-menu-item-card :dish="$dish" :showMenuLink="true" :slug="$restaurant->slug" />
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 4. BRANCHES (Şubeler Listesi) -->
                @if($restaurant->branches && $restaurant->branches->count() > 0)
                    <section class="space-y-4 pt-8 border-t border-warm">
                        <div class="flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-ink font-display">
                                Şubeler ({{ $restaurant->branches->count() }})
                            </h2>
                            <span class="text-xs text-muted font-medium">Kıbrıs Geneli Hizmet Noktaları</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            @foreach($restaurant->branches as $branch)
                                <div class="p-4 rounded-2xl bg-surface border border-warm shadow-2xs space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-sm text-ink">{{ $branch->name }}</span>
                                            @if($branch->is_main)
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold bg-terracotta/10 text-terracotta">Merkez</span>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center gap-1 text-xs font-bold text-amber-500 bg-sand px-2 py-0.5 rounded border border-warm">
                                            ★ {{ number_format($branch->average_rating, 1) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-muted">{{ $branch->address }}</p>
                                    
                                    <div class="flex items-center justify-between pt-2 border-t border-warm text-xs">
                                        <span class="text-muted font-mono">{{ $branch->opening_hours ?? '10:00 - 23:00' }}</span>
                                        <div class="flex items-center gap-3">
                                            @if($branch->phone)
                                                <a href="tel:{{ $branch->phone }}" class="text-terracotta font-bold hover:underline">{{ $branch->phone }}</a>
                                            @endif
                                            <a href="{{ route('restaurant.menu', ['restaurant' => $restaurant->slug, 'branch' => $branch->id]) }}" class="text-ink font-bold hover:text-terracotta">
                                                Menü →
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- 5. REVIEWS & RATING SECTION -->
                <section id="reviews" 
                         class="space-y-6 pt-8 border-t border-warm"
                         x-data="{
                            activeBranchId: '{{ $restaurant->branches->first()->id ?? 0 }}',
                            selectedRating: 5,
                            showForm: false
                         }">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-2xl font-bold text-ink font-display">
                                Misafir Yorumları & Puanlar
                            </h2>
                            <p class="text-xs text-muted mt-1">Anonim olarak deneyiminizi paylaşabilir ve puan verebilirsiniz.</p>
                        </div>

                        <button type="button" 
                                @click="showForm = !showForm"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-terracotta hover:bg-terracotta-dark text-white font-bold text-xs shadow-xs transition-colors shrink-0">
                            <span x-text="showForm ? 'Kapat' : '+ Puan & Yorum Bırak'"></span>
                        </button>
                    </div>

                    <!-- Review Form -->
                    <div x-show="showForm" x-collapse class="p-5 rounded-2xl bg-surface border border-warm space-y-4 shadow-xs">
                        <h3 class="text-sm font-bold text-ink">Yorum Yapın</h3>
                        @foreach($restaurant->branches as $b)
                            <form x-show="activeBranchId == '{{ $b->id }}'" 
                                  action="{{ route('branches.reviews.store', $b->id) }}" 
                                  method="POST" 
                                  class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Puanınız (1 - 5)</label>
                                        <div class="flex items-center gap-2">
                                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                                <button type="button" 
                                                        @click="selectedRating = star"
                                                        class="text-2xl transition-transform hover:scale-110 focus:outline-none">
                                                    <span :class="star <= selectedRating ? 'text-amber-500' : 'text-stone-300'">★</span>
                                                </button>
                                            </template>
                                            <span class="text-xs font-bold text-ink font-mono ml-2" x-text="selectedRating + ' / 5'"></span>
                                        </div>
                                        <input type="hidden" name="rating" :value="selectedRating">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Adınız / Rumuz (İsteğe Bağlı)</label>
                                        <input type="text" 
                                               name="author_name" 
                                               placeholder="Anonim Misafir veya Adınız" 
                                               class="w-full px-4 py-2.5 bg-sand border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-muted uppercase tracking-wider mb-2">Yorumunuz</label>
                                    <textarea name="comment" 
                                              rows="3" 
                                              placeholder="{{ $b->name }} şubesindeki lezzet ve servis nasıldı?" 
                                              class="w-full px-4 py-2.5 bg-sand border border-warm rounded-xl text-xs text-ink focus:outline-none focus:border-terracotta font-medium"></textarea>
                                </div>

                                <div class="flex justify-end gap-2">
                                    <button type="button" @click="showForm = false" class="px-4 py-2 rounded-xl text-xs font-bold text-muted hover:text-ink">İptal</button>
                                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-terracotta text-white font-bold text-xs shadow-xs">Gönder</button>
                                </div>
                            </form>
                        @endforeach
                    </div>

                    <!-- Reviews List -->
                    @foreach($restaurant->branches as $b)
                        <div x-show="activeBranchId == '{{ $b->id }}'" class="space-y-3">
                            @if($b->reviews->isEmpty())
                                <div class="p-6 text-center bg-surface rounded-2xl border border-dashed border-warm text-muted text-xs">
                                    Henüz bu şube için bir yorum yapılmamış. İlk yorumu siz yapın!
                                </div>
                            @else
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($b->reviews as $rev)
                                        <div class="p-4 rounded-2xl bg-surface border border-warm space-y-2 shadow-2xs">
                                            <div class="flex items-center justify-between text-xs">
                                                <span class="font-bold text-ink">{{ $rev->author_name }}</span>
                                                <div class="flex items-center gap-0.5 text-amber-500">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <span class="{{ $i <= $rev->rating ? 'text-amber-500' : 'text-stone-300' }}">★</span>
                                                    @endfor
                                                </div>
                                            </div>
                                            @if($rev->comment)
                                                <p class="text-xs text-ink/80 leading-relaxed">{{ $rev->comment }}</p>
                                            @endif
                                            <span class="text-[10px] text-muted block">{{ $rev->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </section>

            </div>

            <!-- RIGHT 4 COLS: OPERATING HOURS & INTERACTIVE MINI MAP -->
            <div class="lg:col-span-4 space-y-8">

                <!-- 1. WORKING HOURS (vilka.az İş saatları box) -->
                <section class="bg-surface rounded-2xl border border-warm p-6 shadow-2xs space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-warm">
                        <h2 class="text-lg font-bold text-ink font-display">
                            İş Saatleri
                        </h2>
                        <span class="w-2.5 h-2.5 rounded-full {{ $restaurant->isOpenNow() ? 'bg-emerald-500' : 'bg-stone-300' }}"></span>
                    </div>

                    @php
                        $scheduleSource = $restaurant->branches->where('is_main', true)->first() ?? $restaurant->branches->first() ?? $restaurant;
                        $weeklyHours = $scheduleSource->weekly_hours ?? $restaurant->weekly_hours;
                        $daysMap = [
                            'monday' => 'Pazartesi',
                            'tuesday' => 'Salı',
                            'wednesday' => 'Çarşamba',
                            'thursday' => 'Perşembe',
                            'friday' => 'Cuma',
                            'saturday' => 'Cumartesi',
                            'sunday' => 'Pazar',
                        ];
                        $currentDayKey = strtolower(now()->format('l'));
                    @endphp

                    <ul class="space-y-2 text-xs sm:text-sm">
                        @foreach($daysMap as $dayKey => $dayName)
                            @php
                                $dayConfig = is_array($weeklyHours) ? ($weeklyHours[$dayKey] ?? null) : null;
                                $isToday = ($dayKey === $currentDayKey);
                            @endphp
                            <li class="flex items-center justify-between py-1.5 border-b border-warm/60 last:border-0 {{ $isToday ? 'font-bold text-ink bg-sand/60 px-2 rounded-lg' : 'text-muted' }}">
                                <span class="flex items-center gap-1.5">
                                    <span>{{ $dayName }}</span>
                                    @if($isToday)
                                        <span class="text-[10px] px-1.5 py-0.2 bg-terracotta text-white rounded font-bold">Bugün</span>
                                    @endif
                                </span>
                                <div>
                                    @if(!empty($dayConfig['is_closed']))
                                        <span class="text-stone-400">Kapalı</span>
                                    @elseif(!empty($dayConfig['open']) && !empty($dayConfig['close']))
                                        <span class="font-mono text-ink">{{ $dayConfig['open'] }} – {{ $dayConfig['close'] }}</span>
                                    @else
                                        <span class="font-mono text-ink">{{ $scheduleSource->opening_hours ?? '10:00 – 23:00' }}</span>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </section>

                <!-- 2. INTERACTIVE MAP & DIRECTIONS -->
                <section class="bg-surface rounded-2xl border border-warm p-6 shadow-2xs space-y-4">
                    <h2 class="text-lg font-bold text-ink font-display pb-3 border-b border-warm">
                        Konum & Harita
                    </h2>

                    <!-- Leaflet Mini Map -->
                    <div class="w-full h-48 rounded-xl overflow-hidden border border-warm relative"
                         x-data="{
                            initMap() {
                                this.$nextTick(() => {
                                    if (typeof L !== 'undefined') {
                                        const map = L.map($el, {
                                            center: [{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}],
                                            zoom: 15,
                                            scrollWheelZoom: false,
                                            zoomControl: false
                                        });
                                        L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                                            maxZoom: 19
                                        }).addTo(map);
                                        
                                        const pin = L.divIcon({
                                            className: 'custom-pin',
                                            html: `<div style='background:#E85D3F;color:white;padding:4px 8px;border-radius:9999px;font-weight:bold;font-size:10px;box-shadow:0 3px 10px rgba(232,93,63,0.4);border:2px solid white;'>★ {{ addslashes($restaurant->name) }}</div>`,
                                            iconSize: [80, 24],
                                            iconAnchor: [40, 12]
                                        });
                                        L.marker([{{ $restaurant->display_latitude }}, {{ $restaurant->display_longitude }}], { icon: pin }).addTo(map);
                                    }
                                });
                            }
                         }"
                         x-init="initMap()">
                    </div>

                    <p class="text-xs text-muted">{{ $restaurant->display_address }}</p>

                    <a href="https://www.google.com/maps/search/?api=1&query={{ $restaurant->display_latitude }},{{ $restaurant->display_longitude }}" 
                       target="_blank"
                       rel="noopener"
                       class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-sand hover:bg-warm border border-warm text-ink font-bold text-xs shadow-2xs transition-colors text-center">
                        <x-ico name="map-pin" class="w-4 h-4 text-terracotta" />
                        <span>Google Haritalarda Aç ↗</span>
                    </a>
                </section>

            </div>

        </div>

        <!-- SIMILAR PLACES TAG CLOUD (vilka.az Oxşar məkanlar tag strip) -->
        <section class="pt-8 border-t border-warm space-y-4">
            <h2 class="text-2xl font-bold text-ink font-display">
                Benzer Mekanlar ve Keşif
            </h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" 
                   class="rounded-full border border-warm bg-surface hover:bg-sand px-4 py-2 text-xs sm:text-sm font-semibold text-ink shadow-2xs transition-colors">
                    {{ $restaurant->city->name }} Restoranları
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => $restaurant->cuisine]) }}" 
                   class="rounded-full border border-warm bg-surface hover:bg-sand px-4 py-2 text-xs sm:text-sm font-semibold text-ink shadow-2xs transition-colors">
                    Kıbrıs'ta {{ $restaurant->cuisine }} Mekanları
                </a>
                @foreach($restaurant->categories as $cat)
                    <a href="{{ route('restaurants.index', ['category' => $cat->slug]) }}" 
                       class="rounded-full border border-warm bg-surface hover:bg-sand px-4 py-2 text-xs sm:text-sm font-semibold text-ink shadow-2xs transition-colors">
                        {{ $cat->name }}
                    </a>
                @endforeach
                <a href="{{ route('map') }}" 
                   class="rounded-full border border-warm bg-surface hover:bg-sand px-4 py-2 text-xs sm:text-sm font-semibold text-ink shadow-2xs transition-colors">
                    🗺️ Haritada Tüm Restoranlar
                </a>
            </div>
        </section>

        <!-- RELATED RESTAURANTS CARDS -->
        @if($relatedRestaurants->isNotEmpty())
            <section class="pt-8 border-t border-warm space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-ink font-display">
                            {{ $restaurant->city->name }} Çevresindeki Önerilen Mekanlar
                        </h2>
                        <p class="text-xs text-muted mt-0.5">Aynı şehirdeki doğrulanmış diğer mekanlar</p>
                    </div>

                    <a href="{{ route('restaurants.index', ['city' => $restaurant->city->slug]) }}" 
                       class="text-xs font-bold text-terracotta hover:underline">
                        Tümünü İncele →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedRestaurants as $rel)
                        <x-restaurant-card :restaurant="$rel" />
                    @endforeach
                </div>
            </section>
        @endif

    </div>

@endsection
