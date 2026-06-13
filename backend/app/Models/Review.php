<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'protocol_id',
        'rating',
        'title',
        'body',
        'verified_user',
    ];

    protected $casts = [
        'rating'        => 'integer',
        'verified_user' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function protocol(): BelongsTo
    {
        return $this->belongsTo(Protocol::class);
    }

    // ─── Boot ─────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::saved(function (Review $review) {
            $review->protocol->recalculateRating();
        });

        static::deleted(function (Review $review) {
            $review->protocol->recalculateRating();
        });
    }
}
