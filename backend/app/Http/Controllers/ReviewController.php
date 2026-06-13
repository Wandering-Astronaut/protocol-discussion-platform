<?php

namespace App\Http\Controllers;

use App\Models\Protocol;
use App\Models\Review;
use App\Services\TypesenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(protected TypesenseService $typesense) {}

    /**
     * GET /api/protocols/{protocol}/reviews
     */
    public function index(Protocol $protocol, Request $request): JsonResponse
    {
        $reviews = $protocol->reviews()
            ->with('user:id,name,username,avatar')
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 10));

        return response()->json($reviews);
    }

    /**
     * POST /api/protocols/{protocol}/reviews
     */
    public function store(Request $request, Protocol $protocol): JsonResponse
    {
        $data = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:255',
            'body'    => 'nullable|string',
            'user_id' => 'required|exists:users,id',
        ]);

        // Upsert: one review per user per protocol
        $review = Review::updateOrCreate(
            ['user_id' => $data['user_id'], 'protocol_id' => $protocol->id],
            $data + ['protocol_id' => $protocol->id],
        );

        // Review model boot() triggers recalculateRating()
        // Re-sync Typesense after rating change
        $protocol->refresh();
        $this->typesense->upsertDocument('protocols', $protocol->toTypesenseDocument());

        return response()->json($review->load('user:id,name,username,avatar'), 201);
    }

    /**
     * DELETE /api/reviews/{review}
     */
    public function destroy(Review $review): JsonResponse
    {
        $protocol = $review->protocol;
        $review->delete();

        $protocol->refresh();
        $this->typesense->upsertDocument('protocols', $protocol->toTypesenseDocument());

        return response()->json(['message' => 'Review deleted.']);
    }
}
