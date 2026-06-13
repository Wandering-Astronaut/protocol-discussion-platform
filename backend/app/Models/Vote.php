<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Vote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'voteable_type',
        'voteable_id',
        'value',
    ];

    protected $casts = [
        'value' => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voteable(): MorphTo
    {
        return $this->morphTo();
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isUpvote(): bool
    {
        return $this->value === 1;
    }

    public function isDownvote(): bool
    {
        return $this->value === -1;
    }
}
