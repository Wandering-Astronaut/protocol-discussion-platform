<?php
namespace Database\Factories;
use App\Models\Thread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
class VoteFactory extends Factory {
    public function definition(): array {
        return [
            'user_id' => User::factory(),
            'voteable_type' => Thread::class,
            'voteable_id' => Thread::factory(),
            'value' => fake()->randomElement([1, 1, -1]),
        ];
    }
}
