<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotMatchParticipant;

class RiotMatchParticipantRepository
{
    /**
     * @param array $participantData
     * @return RiotMatchParticipant
     */
    public function createOrUpdate(array $participantData): RiotMatchParticipant
    {
        return RiotMatchParticipant::query()->updateOrCreate([
            'match_id' => $participantData['match_id'],
            'puuid' => $participantData['puuid'],
        ], $participantData);
    }
}
