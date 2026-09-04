<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BranchReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'rating',
        'author_name',
        'comment',
        'ip_address',
        'delete_requested',
        'delete_request_reason',
    ];

    protected $casts = [
        'rating' => 'integer',
        'delete_requested' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BranchReviewImage::class, 'branch_review_id');
    }
}
