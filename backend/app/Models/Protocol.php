<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Protocol extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'tags',
        'category',
        'difficulty',
        'duration',
        'avg_rating',
        'review_count',
        'vote_score',
        'status',
        'typesense_id',
    ];

    protected $casts = [
        'tags'         => 'array',
        'avg_rating'   => 'float',
        'review_count' => 'integer',
        'vote_score'   => 'integer',
    ];

    // ─── Relationships ────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'voteable');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function recalculateRating(): void
    {
        $this->avg_rating   = $this->reviews()->avg('rating') ?? 0;
        $this->review_count = $this->reviews()->count();
        $this->save();
    }

    public function recalculateVoteScore(): void
    {
        $this->vote_score = $this->votes()->sum('value');
        $this->save();
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeMostUpvoted($query)
    {
        return $query->orderByDesc('vote_score');
    }

    public function scopeHighestRated($query)
    {
        return $query->orderByDesc('avg_rating');
    }

    public function scopeMostReviewed($query)
    {
        return $query->orderByDesc('review_count');
    }

    // ─── Typesense Document ───────────────────────────────────

    public function toTypesenseDocument(): array
    {
        return [
            'id'           => (string) $this->id,
            'title'        => $this->title,
            'content'      => strip_tags(substr($this->content, 0, 500)),
            'tags'         => $this->tags ?? [],
            'author'       => $this->user?->name ?? 'Unknown',
            'avg_rating'   => (float) $this->avg_rating,
            'vote_score'   => (int) $this->vote_score,
            'review_count' => (int) $this->review_count,
            'created_at'   => $this->created_at ? $this->created_at->timestamp : 0,
        ];
    }
}
