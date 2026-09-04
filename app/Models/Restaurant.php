<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name',
        'slug',
        'cuisine',
        'description',
        'address',
        'latitude',
        'longitude',
        'phone',
        'rating',
        'reviews_count',
        'price_range',
        'image',
        'cover_image',
        'distance',
        'opening_hours',
        'weekly_hours',
        'is_popular',
        'is_new',
        'is_open',
        'has_delivery',
        'min_order',
    ];

    protected $casts = [
        'rating' => 'float',
        'reviews_count' => 'integer',
        'is_popular' => 'boolean',
        'is_new' => 'boolean',
        'is_open' => 'boolean',
        'has_delivery' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
        'weekly_hours' => 'array',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function menuCategories(): HasMany
    {
        return $this->hasMany(MenuCategory::class)->orderBy('order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class)->orderBy('order');
    }

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }

    /**
     * Güncel saate ve şube çalışma planına göre restoranın açık olup olmadığını döner
     */
    public function isOpenNow(): bool
    {
        // Öncelik: Restorana bağlı ana şube veya ilk şubenin çalışma saatleri
        $branch = $this->branches->where('is_main', true)->first() ?? $this->branches->first();
        if ($branch) {
            return $branch->isOpenNow();
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

        return (bool) $this->is_open;
    }

    /**
     * Bugünün çalışma saatini döner (şube bazlı)
     */
    public function getTodayHours(): string
    {
        $branch = $this->branches->where('is_main', true)->first() ?? $this->branches->first();
        if ($branch) {
            return $branch->getTodayHours();
        }

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

    /**
     * Restoranın ana şubesini (veya ilk aktif şubesini) döner
     */
    public function getPrimaryBranchAttribute(): ?Branch
    {
        return $this->branches->where('is_main', true)->first() ?? $this->branches->first();
    }

    /**
     * Konum, Adres ve Şehir bilgilerini şubeden dinamik olarak okur
     */
    public function getDisplayAddressAttribute(): string
    {
        return $this->primary_branch?->address ?? $this->attributes['address'] ?? '';
    }

    public function getDisplayLatitudeAttribute(): ?float
    {
        return $this->primary_branch?->latitude ?? (float) ($this->attributes['latitude'] ?? 35.3403);
    }

    public function getDisplayLongitudeAttribute(): ?float
    {
        return $this->primary_branch?->longitude ?? (float) ($this->attributes['longitude'] ?? 33.3190);
    }

    public function getDisplayCityAttribute(): ?City
    {
        return $this->primary_branch?->city ?? $this->city;
    }
}
