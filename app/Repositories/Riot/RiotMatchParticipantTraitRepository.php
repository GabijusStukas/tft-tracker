<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotMatchParticipantTrait;

class RiotMatchParticipantTraitRepository
{
    /**
     * @param array $traitData
     * @return RiotMatchParticipantTrait
     */
    public function create(array $traitData): RiotMatchParticipantTrait
    {
        return RiotMatchParticipantTrait::query()->create($traitData);
    }
}
