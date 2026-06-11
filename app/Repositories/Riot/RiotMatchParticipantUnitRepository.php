<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotMatchParticipantUnit;

class RiotMatchParticipantUnitRepository
{
    /**
     * @param array $unitData
     * @return RiotMatchParticipantUnit
     */
    public function create(array $unitData): RiotMatchParticipantUnit
    {
        return RiotMatchParticipantUnit::query()->create($unitData);
    }
}
