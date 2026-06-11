<?php

namespace Database\Factories\Riot;

use Illuminate\Database\Eloquent\Factories\Factory;

class RiotLeagueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'account_id'    => $this->faker->randomNumber(),
            'league_id'     => $this->faker->word(),
            'queue_type'    => $this->faker->word(),
            'tier'          => $this->faker->word(),
            'rank'          => $this->faker->word(),
            'league_points' => $this->faker->randomNumber(),
            'wins'          => $this->faker->randomNumber(),
            'losses'        => $this->faker->randomNumber(),
        ];
    }
}
