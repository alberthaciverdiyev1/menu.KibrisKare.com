<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'rating',
        'author_name',
        'comment',
        'ip_address',
        'is_approved',
        'delete_requested',
        'delete_request_reason',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'delete_requested' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
