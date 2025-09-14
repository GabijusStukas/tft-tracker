<?php

namespace Database\Factories;

use App\Models\RiotMatch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotMatch>
 */
class RiotMatchFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'account_id' => $this->faker->randomNumber(),
            'match_id'   => $this->faker->word(),
        ];
    }
}
