<?php

namespace Database\Factories;

use App\Models\RiotAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotAccount>
 */
class RiotSummonerFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_id' => $this->faker->randomNumber(),
            'profile_icon_id' => $this->faker->randomNumber(),
            'summoner_level' => $this->faker->randomNumber(),
        ];
    }
}
