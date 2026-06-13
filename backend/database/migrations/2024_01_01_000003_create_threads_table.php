<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('protocol_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->json('tags')->nullable();
            $table->integer('vote_score')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->string('status')->default('open')->comment('open,closed,pinned');
            $table->string('typesense_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index('vote_score');
            $table->index('protocol_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('threads');
    }
};
