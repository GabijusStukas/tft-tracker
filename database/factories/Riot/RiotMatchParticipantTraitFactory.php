<?php

namespace Database\Factories\Riot;

use App\Models\Riot\RiotMatchParticipant;
use App\Models\Riot\RiotMatchParticipantTrait;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotMatchParticipantTrait>
 */
class RiotMatchParticipantTraitFactory extends Factory
{
    /**
     * @var class-string<RiotMatchParticipantTrait>
     */
    protected $model = RiotMatchParticipantTrait::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'participant_id' => RiotMatchParticipant::factory(),
            'trait_id' => 'TFT13_Trait_' . $this->faker->lexify('?????'),
            'name' => $this->faker->word(),
            'style' => $this->faker->numberBetween(1, 5),
            'num_units' => $this->faker->numberBetween(1, 8),
            'tier_total' => $this->faker->numberBetween(2, 4),
            'tier_current' => $this->faker->numberBetween(1, 4),
            'icon' => $this->faker->imageUrl(),
        ];
    }
}

