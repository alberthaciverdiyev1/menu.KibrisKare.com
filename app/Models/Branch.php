<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'city_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'phone',
        'opening_hours',
        'weekly_hours',
        'is_main',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'is_main' => 'boolean',
        'is_active' => 'boolean',
        'weekly_hours' => 'array',
    ];

    /**
     * Şubenin güncel saate ve haftalık çalışma planına göre açık olup olmadığını döner
     */
    public function isOpenNow(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();
        $dayKey = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        if (!empty($this->weekly_hours) && is_array($this->weekly_hours)) {
            $todayConfig = $this->weekly_hours[$dayKey] ?? null;

            if ($todayConfig) {
                if (!empty($todayConfig['is_closed'])) {
                    return false;
                }

                $open = $todayConfig['open'] ?? null;
                $close = $todayConfig['close'] ?? null;

                if ($open && $close) {
                    if ($open <= $close) {
                        return $currentTime >= $open && $currentTime <= $close;
                    } else {
                        return $currentTime >= $open || $currentTime <= $close;
                    }
                }
            }
        }

        if (!empty($this->opening_hours) && str_contains($this->opening_hours, '-')) {
            $parts = explode('-', $this->opening_hours);
            $open = trim($parts[0]);
            $close = trim($parts[1]);

            if ($open <= $close) {
                return $currentTime >= $open && $currentTime <= $close;
            } else {
                return $currentTime >= $open || $currentTime <= $close;
            }
        }

        return true;
    }

    /**
     * Şubenin bugünkü çalışma saatini döner
     */
    public function getTodayHours(): string
    {
        $now = now();
        $dayKey = strtolower($now->format('l'));

        if (!empty($this->weekly_hours) && is_array($this->weekly_hours)) {
            $todayConfig = $this->weekly_hours[$dayKey] ?? null;
            if ($todayConfig) {
                if (!empty($todayConfig['is_closed'])) {
                    return 'Bugün Kapalı';
                }
                if (!empty($todayConfig['open']) && !empty($todayConfig['close'])) {
                    return $todayConfig['open'] . ' - ' . $todayConfig['close'];
                }
            }
        }

        return $this->opening_hours ?? '10:00 - 23:00';
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}
