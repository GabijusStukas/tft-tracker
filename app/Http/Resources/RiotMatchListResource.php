<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class RiotMatchListResource extends RiotMatchResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = $this->raw_data;

        $mainParticipant = array_find(
            $data['info']['participants'],
            fn($participant) => $participant['puuid'] === $this->account->puuid
        );

        $data['info']['participants'] = $this->withUnitIcons([$mainParticipant]);

        return [
            'puuid'            => $this->account->puuid,
            'match_id'         => $this->match_id,
            'raw_data'         => $data,
            'match_created_at' => $this->match_created_at?->utc()->toISOString(),
        ];
    }
}
