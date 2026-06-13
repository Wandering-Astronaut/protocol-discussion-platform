<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'protocol_id',
        'title',
        'body',
        'tags',
        'vote_score',
        'comment_count',
        'status',
        'typesense_id',
    ];

    protected $casts = [
        'tags'          => 'array',
        'vote_score'    => 'integer',
        'comment_count' => 'integer',
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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id')->orderByDesc('vote_score');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(Comment::class);
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

    public function recalculateCommentCount(): void
    {
        $this->comment_count = $this->allComments()->count();
        $this->save();
    }

    // ─── Scopes ───────────────────────────────────────────────

    public function scopeMostUpvoted($query)
    {
        return $query->orderByDesc('vote_score');
    }

    public function scopeMostCommented($query)
    {
        return $query->orderByDesc('comment_count');
    }

    // ─── Typesense Document ───────────────────────────────────

    public function toTypesenseDocument(): array
    {
        return [
            'id'             => (string) $this->id,
            'title'          => $this->title,
            'body'           => strip_tags(substr($this->body, 0, 500)),
            'tags'           => $this->tags ?? [],
            'author'         => $this->user?->name ?? 'Unknown',
            'protocol_id'    => $this->protocol_id ? (string) $this->protocol_id : null,
            'protocol_title' => $this->protocol?->title ?? null,
            'vote_score'     => (int) $this->vote_score,
            'comment_count'  => (int) $this->comment_count,
            'created_at'     => $this->created_at ? $this->created_at->timestamp : 0,
        ];
    }
}
