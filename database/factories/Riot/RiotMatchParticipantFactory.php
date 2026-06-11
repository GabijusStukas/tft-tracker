<?php

namespace Database\Factories\Riot;

use App\Models\Riot\RiotMatch;
use App\Models\Riot\RiotMatchParticipant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiotMatchParticipant>
 */
class RiotMatchParticipantFactory extends Factory
{
    /**
     * @var class-string<RiotMatchParticipant>
     */
    protected $model = RiotMatchParticipant::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'match_id' => RiotMatch::factory(),
            'puuid' => $this->faker->uuid(),
            'game_name' => $this->faker->userName(),
            'tag_line' => strtoupper($this->faker->bothify('??##')),
            'level' => $this->faker->numberBetween(1, 11),
            'gold_left' => $this->faker->numberBetween(0, 80),
            'placement' => $this->faker->numberBetween(1, 8),
            'last_round' => $this->faker->numberBetween(2, 10),
        ];
    }
}

