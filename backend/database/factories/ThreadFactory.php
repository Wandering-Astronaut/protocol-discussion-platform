<?php

namespace Database\Factories;

use App\Models\Protocol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ThreadFactory extends Factory
{
    private static array $threads = [
        ['title' => 'Week 2 check-in: Gut Reset is changing everything', 'tags' => ['gut-health', 'results', 'check-in']],
        ['title' => 'Can I do the Gut Reset protocol while breastfeeding?', 'tags' => ['gut-health', 'questions', 'pregnancy']],
        ['title' => 'Substitute for bone broth? I\'m vegetarian', 'tags' => ['gut-health', 'vegetarian', 'alternatives']],
        ['title' => 'My circadian rhythm was destroyed by night shifts – 3-month update', 'tags' => ['sleep', 'shift-work', 'results']],
        ['title' => 'Blue light glasses recommendations under $30', 'tags' => ['sleep', 'gear', 'recommendations']],
        ['title' => 'Dopamine detox: Day 5 – this is harder than I thought', 'tags' => ['dopamine', 'mental-health', 'journal']],
        ['title' => 'Did the dopamine detox change anyone else\'s relationship with social media permanently?', 'tags' => ['dopamine', 'social-media', 'discussion']],
        ['title' => 'Breathwork making me dizzy – is this normal?', 'tags' => ['breathwork', 'nervous-system', 'questions']],
        ['title' => 'For those who\'ve done the full 90-day T-optimization: what actually moved the needle?', 'tags' => ['testosterone', 'results', 'discussion']],
        ['title' => 'Cold plunge water temperature thread – post your setups', 'tags' => ['cold-therapy', 'gear', 'community']],
        ['title' => 'Stacking Lion\'s Mane + Bacopa: timing question', 'tags' => ['nootropics', 'stacking', 'questions']],
        ['title' => 'Has anyone gotten bloodwork before/after the anti-inflammatory protocol?', 'tags' => ['inflammation', 'bloodwork', 'results']],
        ['title' => 'Best fermented foods you\'ve actually enjoyed eating', 'tags' => ['gut-health', 'fermented-foods', 'community']],
        ['title' => 'Intermittent fasting and gym – how do you time your workouts?', 'tags' => ['fasting', 'fitness', 'questions']],
        ['title' => 'Longevity protocol: Who else is doing quarterly 72-hour fasts?', 'tags' => ['longevity', 'fasting', 'advanced']],
        ['title' => 'Sauna vs cold plunge: which do you prioritize when time is limited?', 'tags' => ['sauna', 'cold-therapy', 'discussion']],
        ['title' => 'Magnesium forms comparison – glycinate vs threonate vs malate', 'tags' => ['magnesium', 'supplements', 'comparison']],
        ['title' => 'Resources for learning more about vagus nerve stimulation', 'tags' => ['vagus-nerve', 'resources', 'nervous-system']],
        ['title' => 'Tracking progress without expensive labs – what free/cheap tools do you use?', 'tags' => ['tracking', 'biohacking', 'budget']],
        ['title' => 'Anyone experienced "keto flu" during the gut reset elimination phase?', 'tags' => ['gut-health', 'keto', 'side-effects']],
    ];

    private static int $index = 0;

    public function definition(): array
    {
        $data  = self::$threads[self::$index % count(self::$threads)];
        self::$index++;

        $bodies = [
            "I wanted to share my experience and get some feedback from others who've done this. Starting out I was pretty skeptical but by the end of the first week I noticed real changes. Has anyone else had a similar experience?\n\nMy main question is whether the timing matters for when you take the supplements. I've been doing mornings but wondering if evening would be better.",
            "Long-time lurker, first-time poster. I've been researching this protocol for about 3 months before committing. Just completed the full duration and wanted to give an honest, unbiased breakdown.\n\nThe first two weeks were genuinely rough – fatigue, brain fog, some irritability. Week 3 is when things shifted. By the end I'd describe the benefits as: better sleep quality (tracked via Oura), more stable energy throughout the day, and noticeably improved mood baseline.",
            "Quick question for the community: I travel frequently for work (3-4 times a month) and I'm wondering how people maintain these protocols while traveling. Particularly the sleep-related ones, since hotel rooms are usually terrible for sleep hygiene.\n\nAny practical hacks you've found that actually work in the field?",
            "I've done a lot of research and tried many wellness protocols over the years. Here's my honest take after completing this one:\n\nWhat worked: the sleep optimization components made an immediate measurable difference. The dietary changes were hard the first week, then surprisingly easy once I got into a rhythm.\n\nWhat didn't work as well for me: the supplement stack felt like too many pills. I eventually simplified to the 3 most evidence-backed items and felt equally good.",
            "Sharing this because I couldn't find good info when I was starting: the brand of supplement really matters here. I got dramatically different results when I switched from a cheap brand to a higher-quality standardized extract. Happy to share what I'm using if anyone wants to message me.",
        ];

        return [
            'user_id'     => User::factory(),
            'protocol_id' => fake()->optional(0.8)->passthrough(null),
            'title'       => $data['title'],
            'body'        => fake()->randomElement($bodies),
            'tags'        => $data['tags'],
            'vote_score'  => 0,
            'comment_count' => 0,
            'status'      => 'open',
        ];
    }
}
