<?php

namespace Database\Factories\Riot;

use App\Models\Riot\RiotMatchParticipant;
use App\Models\Riot\RiotMatchParticipantUnit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotMatchParticipantUnit>
 */
class RiotMatchParticipantUnitFactory extends Factory
{
    /**
     * @var class-string<RiotMatchParticipantUnit>
     */
    protected $model = RiotMatchParticipantUnit::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_id' => RiotMatchParticipant::factory(),
            'character_id' => 'TFT13_' . $this->faker->lexify('????????'),
            'name' => $this->faker->firstName(),
            'tier' => $this->faker->numberBetween(1, 3),
            'icon' => $this->faker->imageUrl(),
            'items' => [
                $this->faker->numberBetween(1, 99),
                $this->faker->numberBetween(1, 99),
                $this->faker->numberBetween(1, 99),
            ],
        ];
    }
}

