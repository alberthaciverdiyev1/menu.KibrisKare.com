<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'restaurant_id',
        'menu_category_id',
        'name',
        'description',
        'price',
        'currency',
        'image',
        'is_popular',
        'is_chef_special',
        'is_vegetarian',
        'is_spicy',
        'allergens',
        'order',
    ];

    protected $casts = [
        'price' => 'float',
        'is_popular' => 'boolean',
        'is_chef_special' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_spicy' => 'boolean',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function menuCategory(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class);
    }
}
