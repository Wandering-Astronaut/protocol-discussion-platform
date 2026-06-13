<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProtocolController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ── Health check ────────────────────────────────────────────
Route::get('/health', fn () => response()->json(['status' => 'ok', 'timestamp' => now()]));

// ── Typesense search endpoints ───────────────────────────────
Route::get('/protocols/search', [ProtocolController::class, 'search']);
Route::get('/threads/search',   [ThreadController::class,  'search']);

// ── Protocols ────────────────────────────────────────────────
Route::apiResource('protocols', ProtocolController::class);
Route::get('protocols/{protocol}/reviews', [ReviewController::class, 'index']);
Route::post('protocols/{protocol}/reviews', [ReviewController::class, 'store']);

// ── Threads ──────────────────────────────────────────────────
Route::apiResource('threads', ThreadController::class);
Route::get('threads/{thread}/comments',  [CommentController::class, 'index']);
Route::post('threads/{thread}/comments', [CommentController::class, 'store']);

// ── Comments ─────────────────────────────────────────────────
Route::put('comments/{comment}',    [CommentController::class, 'update']);
Route::delete('comments/{comment}', [CommentController::class, 'destroy']);

// ── Reviews ──────────────────────────────────────────────────
Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

// ── Votes ────────────────────────────────────────────────────
Route::post('vote', [VoteController::class, 'vote']);
Route::get('votes/user/{userId}', [VoteController::class, 'userVotes']);

// ── Typesense reindex (optional API route) ───────────────────
Route::post('admin/reindex', function () {
    \Illuminate\Support\Facades\Artisan::call('typesense:reindex', ['--fresh' => true]);
    return response()->json(['message' => 'Reindex complete.']);
});
