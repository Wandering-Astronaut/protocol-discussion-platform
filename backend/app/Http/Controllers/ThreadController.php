<?php

namespace App\Http\Controllers;

use App\Models\Thread;
use App\Services\TypesenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ThreadController extends Controller
{
    public function __construct(protected TypesenseService $typesense) {}

    /**
     * GET /api/threads
     */
    public function index(Request $request): JsonResponse
    {
        $query = Thread::with(['user:id,name,username,avatar', 'protocol:id,title']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($protocolId = $request->input('protocol_id')) {
            $query->where('protocol_id', $protocolId);
        }

        if ($tag = $request->input('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        match ($request->input('sort', 'recent')) {
            'most_upvoted'   => $query->orderByDesc('vote_score'),
            'most_commented' => $query->orderByDesc('comment_count'),
            default          => $query->orderByDesc('created_at'),
        };

        $threads = $query->paginate($request->input('per_page', 15));

        return response()->json($threads);
    }

    /**
     * POST /api/threads
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'body'        => 'required|string',
            'tags'        => 'nullable|array',
            'tags.*'      => 'string|max:50',
            'protocol_id' => 'nullable|exists:protocols,id',
            'user_id'     => 'required|exists:users,id',
        ]);

        $thread = Thread::create($data);
        $thread->load(['user:id,name,username,avatar', 'protocol:id,title']);

        $this->typesense->upsertDocument('threads', $thread->toTypesenseDocument());

        return response()->json($thread, 201);
    }

    /**
     * GET /api/threads/{thread}
     */
    public function show(Thread $thread): JsonResponse
    {
        $thread->load([
            'user:id,name,username,avatar',
            'protocol:id,title,category',
            'comments' => function ($q) {
                $q->with([
                    'user:id,name,username,avatar',
                    'replies' => function ($q2) {
                        $q2->with([
                            'user:id,name,username,avatar',
                            'replies.user:id,name,username,avatar',
                        ]);
                    },
                ]);
            },
        ]);

        return response()->json($thread);
    }

    /**
     * PUT /api/threads/{thread}
     */
    public function update(Request $request, Thread $thread): JsonResponse
    {
        $data = $request->validate([
            'title'  => 'sometimes|string|max:255',
            'body'   => 'sometimes|string',
            'tags'   => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'nullable|in:open,closed,pinned',
        ]);

        $thread->update($data);
        $this->typesense->upsertDocument('threads', $thread->fresh()->toTypesenseDocument());

        return response()->json($thread->load('user:id,name,username,avatar'));
    }

    /**
     * DELETE /api/threads/{thread}
     */
    public function destroy(Thread $thread): JsonResponse
    {
        $this->typesense->deleteDocument('threads', (string) $thread->id);
        $thread->delete();

        return response()->json(['message' => 'Thread deleted.']);
    }

    /**
     * GET /api/threads/search
     */
    public function search(Request $request): JsonResponse
    {
        $q    = $request->input('q', '*');
        $page = (int) $request->input('page', 1);
        $sort = $request->input('sort', 'recent');

        $sortBy = match ($sort) {
            'most_upvoted'   => 'vote_score:desc',
            'most_commented' => 'comment_count:desc',
            default          => 'created_at:desc',
        };

        $params = [
            'q'                     => $q,
            'query_by'              => 'title,body,tags,author',
            'query_by_weights'      => '4,2,3,1',
            'sort_by'               => $sortBy,
            'per_page'              => 15,
            'page'                  => $page,
            'facet_by'              => 'tags,author',
            'highlight_full_fields' => 'title',
        ];

        $filters = [];
        if ($protocolId = $request->input('protocol_id')) {
            $filters[] = "protocol_id:={$protocolId}";
        }
        if ($tag = $request->input('tag')) {
            $filters[] = "tags:=[{$tag}]";
        }
        if ($filters) {
            $params['filter_by'] = implode(' && ', $filters);
        }

        $result = $this->typesense->search('threads', $params);

        return response()->json($result);
    }
}
