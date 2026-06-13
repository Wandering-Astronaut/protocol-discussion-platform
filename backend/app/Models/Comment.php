<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'thread_id',
        'parent_id',
        'body',
        'vote_score',
        'depth',
        'is_deleted',
    ];

    protected $casts = [
        'vote_score' => 'integer',
        'depth'      => 'integer',
        'is_deleted' => 'boolean',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderByDesc('vote_score');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function recalculateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }

    // ─── Body accessor (hide deleted content) ─────────────────

    public function getBodyAttribute($value): string
    {
        if ($this->is_deleted) {
            return '[deleted]';
        }
        return $value;
    }
}
