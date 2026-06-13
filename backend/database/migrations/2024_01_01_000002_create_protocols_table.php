<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('content');
            $table->json('tags')->nullable();
            $table->string('category')->default('general');
            $table->string('difficulty')->default('beginner')->comment('beginner,intermediate,advanced');
            $table->string('duration')->nullable()->comment('e.g. 30 days, 8 weeks');
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('review_count')->default(0);
            $table->integer('vote_score')->default(0);
            $table->string('status')->default('published')->comment('draft,published,archived');
            $table->string('typesense_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('vote_score');
            $table->index('avg_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocols');
    }
};
