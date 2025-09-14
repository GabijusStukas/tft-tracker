<?php

namespace Database\Factories;

use App\Models\RiotAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotAccount>
 */
class RiotAccountFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'puuid'     => $this->faker->uuid(),
            'game_name' => $this->faker->userName(),
            'tag_line'  => $this->faker->randomNumber(4, true),
            'region'    => $this->faker->randomElement(['euw1', 'eun1']),
            'game'      => $this->faker->randomElement(['tft', 'lol'])
        ];
    }
}
