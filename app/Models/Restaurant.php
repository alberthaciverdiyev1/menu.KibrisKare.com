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
}
