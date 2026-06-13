<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->morphs('voteable'); // voteable_type, voteable_id
            $table->tinyInteger('value')->comment('1 = upvote, -1 = downvote');
            $table->timestamps();

            // One vote per user per voteable item
            $table->unique(['user_id', 'voteable_type', 'voteable_id'], 'votes_user_voteable_unique');
    
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
