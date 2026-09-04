<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BranchReviewImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_review_id',
        'image_path',
    ];

    public function review(): BelongsTo
    {
        return $this->belongsTo(BranchReview::class, 'branch_review_id');
    }

    public function getUrlAttribute(): string
    {
        if (\Illuminate\Support\Str::startsWith($this->image_path, ['http://', 'https://', '/'])) {
            return $this->image_path;
        }

        return Storage::disk('public')->url($this->image_path);
    }
}
