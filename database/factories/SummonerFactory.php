<?php

namespace Database\Factories;

use App\Models\Summoner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Summoner>
 */
class SummonerFactory extends Factory
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
        ];
    }
}
