<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Protocol;
use App\Models\Review;
use App\Models\Thread;
use App\Models\User;
use App\Models\Vote;
use App\Services\TypesenseService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🌱 Seeding database...');

        // ─── Users ────────────────────────────────────────────
        $this->command->info('Creating users...');

        // Create one known demo user
        $demoUser = User::create([
            'name'              => 'Alex Rivera',
            'username'          => 'alexrivera',
            'email'             => 'demo@justholistics.com',
            'bio'               => 'Holistic health researcher and biohacker. 10 years of self-experimentation.',
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
        ]);

        // Create 19 more random users
        $users = User::factory()->count(19)->create();
        $allUsers = $users->prepend($demoUser);

        // ─── Protocols ────────────────────────────────────────
        $this->command->info('Creating 13 protocols...');

        $protocols = collect();
        for ($i = 0; $i < 13; $i++) {
            $protocol = Protocol::factory()->create([
                'user_id' => $allUsers->random()->id,
            ]);
            $protocols->push($protocol);
        }

        // ─── Reviews ──────────────────────────────────────────
        $this->command->info('Creating reviews...');

        $usedCombinations = [];
        foreach ($protocols as $protocol) {
            $reviewCount = rand(2, 6);
            $reviewUsers = $allUsers->shuffle()->take($reviewCount);

            foreach ($reviewUsers as $user) {
                $key = "{$user->id}_{$protocol->id}";
                if (isset($usedCombinations[$key])) continue;
                $usedCombinations[$key] = true;

                Review::factory()->create([
                    'user_id'     => $user->id,
                    'protocol_id' => $protocol->id,
                ]);
            }

            // Recalculate after all reviews
            $protocol->recalculateRating();
        }

        // ─── Threads ──────────────────────────────────────────
        $this->command->info('Creating 20 threads...');

        $threads = collect();
        for ($i = 0; $i < 20; $i++) {
            $protocol = ($i < 15) ? $protocols->random() : null;
            $thread = Thread::factory()->create([
                'user_id'     => $allUsers->random()->id,
                'protocol_id' => $protocol?->id,
            ]);
            $threads->push($thread);
        }

        // ─── Comments & Nested Replies ────────────────────────
        $this->command->info('Creating comments with nested replies...');

        foreach ($threads as $thread) {
            $commentCount = rand(3, 8);

            for ($c = 0; $c < $commentCount; $c++) {
                $comment = Comment::factory()->create([
                    'user_id'   => $allUsers->random()->id,
                    'thread_id' => $thread->id,
                    'parent_id' => null,
                    'depth'     => 0,
                ]);

                // 60% chance of replies
                if (rand(1, 10) <= 6) {
                    $replyCount = rand(1, 4);
                    for ($r = 0; $r < $replyCount; $r++) {
                        $reply = Comment::factory()->create([
                            'user_id'   => $allUsers->random()->id,
                            'thread_id' => $thread->id,
                            'parent_id' => $comment->id,
                            'depth'     => 1,
                        ]);

                        // 30% chance of deeper nesting
                        if (rand(1, 10) <= 3) {
                            Comment::factory()->create([
                                'user_id'   => $allUsers->random()->id,
                                'thread_id' => $thread->id,
                                'parent_id' => $reply->id,
                                'depth'     => 2,
                            ]);
                        }
                    }
                }
            }

            $thread->recalculateCommentCount();
        }

        // ─── Votes on Threads ─────────────────────────────────
        $this->command->info('Creating votes...');

        $usedVotes = [];
        foreach ($threads as $thread) {
            $voterCount = rand(3, 12);
            $voters = $allUsers->shuffle()->take($voterCount);

            foreach ($voters as $voter) {
                $key = "thread_{$voter->id}_{$thread->id}";
                if (isset($usedVotes[$key])) continue;
                $usedVotes[$key] = true;

                Vote::create([
                    'user_id'       => $voter->id,
                    'voteable_type' => Thread::class,
                    'voteable_id'   => $thread->id,
                    'value'         => fake()->randomElement([1, 1, 1, -1]), // 75% upvote
                ]);
            }

            $thread->recalculateVoteScore();
        }

        // ─── Votes on Comments ────────────────────────────────
        $allComments = Comment::all();
        foreach ($allComments as $comment) {
            $voterCount = rand(0, 5);
            $voters = $allUsers->shuffle()->take($voterCount);

            foreach ($voters as $voter) {
                $key = "comment_{$voter->id}_{$comment->id}";
                if (isset($usedVotes[$key])) continue;
                $usedVotes[$key] = true;

                Vote::create([
                    'user_id'       => $voter->id,
                    'voteable_type' => Comment::class,
                    'voteable_id'   => $comment->id,
                    'value'         => fake()->randomElement([1, 1, -1]),
                ]);
            }

            $comment->recalculateVoteScore();
        }

        // ─── Typesense Indexing ───────────────────────────────
        $this->command->info('Indexing data in Typesense...');

        try {
            $typesense = app(TypesenseService::class);
            $typesense->ensureCollectionsExist();

            $protocolDocs = $protocols->load('user')->map->toTypesenseDocument()->toArray();
            $typesense->bulkImport('protocols', $protocolDocs);

            $threadDocs = $threads->load(['user', 'protocol'])->map->toTypesenseDocument()->toArray();
            $typesense->bulkImport('threads', $threadDocs);

            $this->command->info('✅ Typesense indexing complete!');
        } catch (\Exception $e) {
            $this->command->warn('⚠️  Typesense indexing failed: ' . $e->getMessage());
            $this->command->warn('Run php artisan typesense:reindex after configuring Typesense.');
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->table(
            ['Resource', 'Count'],
            [
                ['Users', User::count()],
                ['Protocols', Protocol::count()],
                ['Threads', Thread::count()],
                ['Comments', Comment::count()],
                ['Reviews', Review::count()],
                ['Votes', Vote::count()],
            ]
        );
    }
}
