<?php

namespace Database\Factories;

use App\Models\Protocol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    private static array $reviews = [
        5 => [
            ['title' => 'Life-changing protocol', 'body' => 'I was skeptical going in but the results have been remarkable. By week 3 my energy levels, sleep quality, and mood were all noticeably improved. The structure of this protocol is thoughtful and the science behind each recommendation is solid. Highly recommend to anyone who is committed to doing it properly.'],
            ['title' => 'Best protocol I\'ve tried', 'body' => 'After trying many different approaches over the years, this one produced the most consistent and lasting results. The key is that it addresses multiple systems simultaneously rather than just one variable. I tracked my biomarkers before and after – the data speaks for itself.'],
            ['title' => 'Exceeded expectations', 'body' => 'The first week is genuinely difficult but the payoff is worth it. I\'ve shared this with three friends who\'ve all had positive results. The supplement recommendations are evidence-based and not just trendy add-ons.'],
        ],
        4 => [
            ['title' => 'Very effective with minor modifications', 'body' => 'Followed this protocol for the full duration and saw meaningful improvements. I made a few small modifications for my lifestyle and it worked even better. The core framework is excellent. Took off one star only because some of the supplement recommendations are expensive.'],
            ['title' => 'Solid results, takes commitment', 'body' => 'This isn\'t a quick fix – it requires real commitment. But if you follow it properly the results are genuine. I\'d say 80% of the benefits came from the lifestyle recommendations and 20% from the supplements, which is probably the right ratio.'],
        ],
        3 => [
            ['title' => 'Mixed results for me', 'body' => 'I experienced some of the promised benefits but not all. Sleep improved noticeably but energy and cognitive benefits were more modest. This may be highly individual. I\'d suggest doing a fuller health assessment before starting to see if this protocol addresses your specific needs.'],
        ],
    ];

    public function definition(): array
    {
        $rating = fake()->randomElement([5, 5, 5, 4, 4, 3]);
        $review = fake()->randomElement(self::$reviews[$rating]);

        return [
            'user_id'      => User::factory(),
            'protocol_id'  => Protocol::factory(),
            'rating'       => $rating,
            'title'        => $review['title'],
            'body'         => $review['body'],
            'verified_user' => fake()->boolean(60),
        ];
    }
}
