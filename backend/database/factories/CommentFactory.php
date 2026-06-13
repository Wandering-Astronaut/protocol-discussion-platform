<?php

namespace Database\Factories;

use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    private static array $comments = [
        "Really appreciate you sharing this. I had almost the exact same experience in week 2. The fatigue is real but it does pass.",
        "Have you tried taking the supplement with food? That made a significant difference for absorption in my experience.",
        "This is the most underrated aspect of the whole protocol. Most people skip this part and wonder why they're not getting results.",
        "I've done this twice now (once last year, once recently) and I get noticeably better results the second time around. Your body seems to know the pattern.",
        "Great question. I'd suggest starting with just the foundational elements before adding the full supplement stack. Less overwhelming.",
        "The science on this is actually really solid. There are several well-designed RCTs supporting this specific approach.",
        "Curious what your baseline markers were before starting? Would help contextualize your results.",
        "This was exactly my experience too. Week 1 is rough, week 2 you start to see glimmers, week 3 and beyond is where the real changes happen.",
        "I asked my functional medicine doc about this and she confirmed it's a common response. Worth pushing through.",
        "For the travel question – I've found that getting sunlight immediately after landing helps reset the circadian clock faster than anything else.",
        "Agree on the supplement quality point. Third-party tested brands make a measurable difference. Look for NSF or USP certification.",
        "This is controversial but I actually found that doing a shorter, more intense version worked better for my schedule. Happy to share what I modified.",
        "The emotional component is often underestimated. Journaling during this protocol really helped me identify patterns I wasn't aware of.",
        "How long until you felt the cognitive benefits? I'm on day 12 and still waiting...",
        "The cold exposure part was hardest for me to stick with, but it also gave me the clearest benefits. Anyone else find this?",
    ];

    public function definition(): array
    {
        return [
            'user_id'   => User::factory(),
            'thread_id' => Thread::factory(),
            'parent_id' => null,
            'body'      => fake()->randomElement(self::$comments),
            'vote_score' => 0,
            'depth'     => 0,
            'is_deleted' => false,
        ];
    }
}
