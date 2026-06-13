<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Thread;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * GET /api/threads/{thread}/comments
     */
    public function index(Thread $thread): JsonResponse
    {
        $comments = $thread->comments()
            ->with([
                'user:id,name,username,avatar',
                'replies' => function ($q) {
                    $q->with([
                        'user:id,name,username,avatar',
                        'replies.user:id,name,username,avatar',
                    ])->orderByDesc('vote_score');
                },
            ])
            ->orderByDesc('vote_score')
            ->get();

        return response()->json($comments);
    }

    /**
     * POST /api/threads/{thread}/comments
     */
    public function store(Request $request, Thread $thread): JsonResponse
    {
        $data = $request->validate([
            'body'      => 'required|string',
            'parent_id' => 'nullable|exists:comments,id',
            'user_id'   => 'required|exists:users,id',
        ]);

        $depth = 0;
        if ($data['parent_id'] ?? null) {
            $parent = Comment::find($data['parent_id']);
            $depth  = ($parent->depth ?? 0) + 1;
        }

        $comment = $thread->allComments()->create([
            ...$data,
            'depth' => $depth,
        ]);

        // Update comment count on thread
        $thread->increment('comment_count');

        $comment->load('user:id,name,username,avatar');

        return response()->json($comment, 201);
    }

    /**
     * PUT /api/comments/{comment}
     */
    public function update(Request $request, Comment $comment): JsonResponse
    {
        $data = $request->validate([
            'body' => 'required|string',
        ]);

        $comment->update($data);

        return response()->json($comment->load('user:id,name,username,avatar'));
    }

    /**
     * DELETE /api/comments/{comment}
     */
    public function destroy(Comment $comment): JsonResponse
    {
        // Soft-delete: mark body as [deleted] but keep thread structure
        $comment->update(['is_deleted' => true]);

        return response()->json(['message' => 'Comment deleted.']);
    }
}
