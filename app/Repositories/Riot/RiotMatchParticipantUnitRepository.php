<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotMatchParticipantUnit;

class RiotMatchParticipantUnitRepository
{
    /**
     * @param array $unitData
     * @return RiotMatchParticipantUnit
     */
    public function createOrUpdate(array $unitData): RiotMatchParticipantUnit
    {
        return RiotMatchParticipantUnit::query()->updateOrCreate([
            'participant_id' => $unitData['participant_id'],
        ], $unitData);
    }
}
