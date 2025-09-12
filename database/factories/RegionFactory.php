<?php

namespace Database\Factories;

use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cluster' => $this->faker->randomElement(['americas', 'europe', 'asia', 'sea']),
            'region'  => $this->faker->randomElement(['euw1', 'eun1', 'na1', 'kr', 'jp1', 'br1', 'oc1', 'tr1']),
        ];
    }
}
