<!-- ================= ÇALIŞMA SAATLERİ ================= -->
<div class="bg-surface rounded-2xl p-6 shadow-2xs space-y-3">
    <div class="flex items-center gap-2.5 pb-2">
        <x-ico name="clock" class="w-5 h-5 text-terracotta" />
        <h3 class="text-base font-bold text-ink">Çalışma Saatleri</h3>
    </div>

    <div class="space-y-0.5 text-xs sm:text-sm">
        @foreach($days as $key => $name)
            @php
                $cfg = is_array($weekly) ? ($weekly[$key] ?? null) : null;
                $isToday = $key === $todayKey;
                $closed = (is_array($cfg) && !empty($cfg['is_closed'])) || $key === 'sunday';
                $range = !empty($cfg['open']) && !empty($cfg['close']) ? $cfg['open'] . '–' . $cfg['close'] : '09:00–21:00';
                $time = $closed ? 'Kapalı' : $range;
            @endphp
            <div class="flex items-center justify-between py-2.5 px-3 rounded-lg border-b border-stone-100 last:border-b-0 {{ $isToday ? 'bg-terracotta/10 text-terracotta font-bold' : 'text-muted' }}">
                <span class="{{ $isToday ? 'text-terracotta font-bold' : 'text-muted font-medium' }}">{{ $name }}</span>
                <span class="{{ $isToday ? 'text-terracotta font-bold font-mono' : ($closed ? 'text-ink font-medium' : 'text-ink font-mono font-medium') }}">
                    {{ $time }}
                </span>
            </div>
        @endforeach
    </div>
</div>
