<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use App\Models\Vote;
use App\Services\TypesenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct(protected TypesenseService $typesense) {}

    /**
     * POST /api/vote
     * Body: { user_id, voteable_type (thread|comment), voteable_id, value (1|-1) }
     */
    public function vote(Request $request): JsonResponse
    {
        $data = $request->validate([
            'user_id'      => 'required|exists:users,id',
            'voteable_type' => 'required|in:thread,comment',
            'voteable_id'  => 'required|integer',
            'value'        => 'required|in:-1,1',
        ]);

        $modelClass = match ($data['voteable_type']) {
            'thread'  => Thread::class,
            'comment' => Comment::class,
        };

        $voteable = $modelClass::findOrFail($data['voteable_id']);

        // Check existing vote
        $existing = Vote::where([
            'user_id'      => $data['user_id'],
            'voteable_type' => $modelClass,
            'voteable_id'  => $data['voteable_id'],
        ])->first();

        if ($existing) {
            if ($existing->value === (int) $data['value']) {
                // Toggle off (remove vote)
                $existing->delete();
                $voteable->recalculateVoteScore();

                $this->syncTypesense($voteable, $data['voteable_type']);

                return response()->json([
                    'action'     => 'removed',
                    'vote_score' => $voteable->fresh()->vote_score,
                    'user_vote'  => null,
                ]);
            } else {
                // Change vote direction
                $existing->update(['value' => $data['value']]);
                $voteable->recalculateVoteScore();

                $this->syncTypesense($voteable, $data['voteable_type']);

                return response()->json([
                    'action'     => 'changed',
                    'vote_score' => $voteable->fresh()->vote_score,
                    'user_vote'  => (int) $data['value'],
                ]);
            }
        }

        // New vote
        Vote::create([
            'user_id'      => $data['user_id'],
            'voteable_type' => $modelClass,
            'voteable_id'  => $data['voteable_id'],
            'value'        => $data['value'],
        ]);

        $voteable->recalculateVoteScore();
        $this->syncTypesense($voteable, $data['voteable_type']);

        return response()->json([
            'action'     => 'created',
            'vote_score' => $voteable->fresh()->vote_score,
            'user_vote'  => (int) $data['value'],
        ], 201);
    }

    /**
     * GET /api/votes/user/{userId}
     * Returns all votes by a user (for rendering current vote state)
     */
    public function userVotes(Request $request, int $userId): JsonResponse
    {
        $votes = Vote::where('user_id', $userId)->get()
            ->mapWithKeys(fn ($v) => [
                "{$v->voteable_type}_{$v->voteable_id}" => $v->value
            ]);

        return response()->json($votes);
    }

    private function syncTypesense(mixed $voteable, string $type): void
    {
        if ($type === 'thread') {
            $this->typesense->upsertDocument('threads', $voteable->fresh()->toTypesenseDocument());
        }
        // Comments are not indexed in Typesense (per brief), but Protocol vote_score is handled separately
    }
}
