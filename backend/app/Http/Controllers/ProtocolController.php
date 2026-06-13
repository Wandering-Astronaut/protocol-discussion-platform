<?php

namespace App\Http\Controllers;

use App\Models\Protocol;
use App\Services\TypesenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProtocolController extends Controller
{
    public function __construct(protected TypesenseService $typesense) {}

    /**
     * GET /api/protocols
     * Supports: search, sort, filter, pagination
     */
    public function index(Request $request): JsonResponse
    {
        $query = Protocol::with(['user:id,name,username,avatar'])
            ->published();

        // Text search (DB fallback when Typesense not used directly)
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Tag filter
        if ($tag = $request->input('tag')) {
            $query->whereJsonContains('tags', $tag);
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Sorting
        match ($request->input('sort', 'recent')) {
            'most_upvoted'  => $query->orderByDesc('vote_score'),
            'highest_rated' => $query->orderByDesc('avg_rating'),
            'most_reviewed' => $query->orderByDesc('review_count'),
            default         => $query->orderByDesc('created_at'),
        };

        $protocols = $query->paginate($request->input('per_page', 12));

        return response()->json($protocols);
    }

    /**
     * POST /api/protocols
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title'      => 'required|string|max:255',
            'content'    => 'required|string',
            'tags'       => 'nullable|array',
            'tags.*'     => 'string|max:50',
            'category'   => 'nullable|string|max:100',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'duration'   => 'nullable|string|max:100',
            'user_id'    => 'required|exists:users,id',
        ]);

        $protocol = Protocol::create($data);
        $protocol->load('user:id,name,username,avatar');

        // Index in Typesense
        $this->typesense->upsertDocument('protocols', $protocol->toTypesenseDocument());

        return response()->json($protocol, 201);
    }

    /**
     * GET /api/protocols/{protocol}
     */
    public function show(Protocol $protocol): JsonResponse
    {
        $protocol->load([
            'user:id,name,username,avatar',
            'threads' => function ($q) {
                $q->with('user:id,name,username,avatar')
                  ->orderByDesc('vote_score')
                  ->limit(10);
            },
            'reviews' => function ($q) {
                $q->with('user:id,name,username,avatar')
                  ->latest()
                  ->limit(10);
            },
        ]);

        return response()->json($protocol);
    }

    /**
     * PUT /api/protocols/{protocol}
     */
    public function update(Request $request, Protocol $protocol): JsonResponse
    {
        $data = $request->validate([
            'title'      => 'sometimes|string|max:255',
            'content'    => 'sometimes|string',
            'tags'       => 'nullable|array',
            'tags.*'     => 'string|max:50',
            'category'   => 'nullable|string|max:100',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'duration'   => 'nullable|string|max:100',
            'status'     => 'nullable|in:draft,published,archived',
        ]);

        $protocol->update($data);
        $protocol->load('user:id,name,username,avatar');

        // Re-sync Typesense
        $this->typesense->upsertDocument('protocols', $protocol->fresh()->toTypesenseDocument());

        return response()->json($protocol);
    }

    /**
     * DELETE /api/protocols/{protocol}
     */
    public function destroy(Protocol $protocol): JsonResponse
    {
        $this->typesense->deleteDocument('protocols', (string) $protocol->id);
        $protocol->delete();

        return response()->json(['message' => 'Protocol deleted.']);
    }

    /**
     * GET /api/protocols/search
     * Full Typesense-powered search with facets
     */
    public function search(Request $request): JsonResponse
    {
        $q    = $request->input('q', '*');
        $page = (int) $request->input('page', 1);
        $sort = $request->input('sort', 'recent');

        $sortBy = match ($sort) {
            'most_upvoted'  => 'vote_score:desc',
            'highest_rated' => 'avg_rating:desc',
            'most_reviewed' => 'review_count:desc',
            default         => 'created_at:desc',
        };

        $params = [
            'q'                  => $q,
            'query_by'           => 'title,content,tags,author',
            'query_by_weights'   => '4,2,3,1',
            'sort_by'            => $sortBy,
            'per_page'           => 12,
            'page'               => $page,
            'facet_by'           => 'tags,author',
            'highlight_full_fields' => 'title',
        ];

        if ($tag = $request->input('tag')) {
            $params['filter_by'] = "tags:=[{$tag}]";
        }

        $result = $this->typesense->search('protocols', $params);

        return response()->json($result);
    }
}
