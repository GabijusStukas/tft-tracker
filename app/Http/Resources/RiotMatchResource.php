<?php

namespace App\Http\Resources;

use App\Models\Riot\RiotMatchParticipant;
use App\Models\Riot\RiotMatchParticipantTrait;
use App\Models\Riot\RiotMatchParticipantUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiotMatchResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $participants = [];
        /** @var RiotMatchParticipant $participant */
        foreach ($this->participants as $participant) {
            $traits = $participant->traits->map(function (RiotMatchParticipantTrait $trait) {
                return [
                    'name'         => $trait->name,
                    'tier_current' => $trait->tier_current,
                    'tier_total'   => $trait->tier_total,
                    'num_units'    => $trait->num_units,
                    'style'        => $trait->style,
                    'icon'         => $trait->icon,
                ];
            });

            $units = $participant->units->map(function (RiotMatchParticipantUnit $unit) {
                return [
                    'name' => $unit->name ?? $unit->character_id,
                    'tier' => $unit->tier,
                    'rarity' => $unit->rarity,
                    'icon' => $unit->icon,
                    'items' => $unit->items,
                ];
            });

            $participants[] = [
                'game_name'  => $participant->game_name,
                'tag_line'   => $participant->tag_line,
                'level'      => $participant->level,
                'gold_left'  => $participant->gold_left,
                'placement'  => $participant->placement,
                'last_round' => $participant->last_round,
                'traits'     => $traits,
                'units'      => $units,
            ];
        }

        return [
            'match_id'         => $this->match_id,
            'queue_name'       => $this->queue_name,
            'match_created_at' => $this->match_created_at?->utc()->toISOString(),
            'participants'     => $participants,
        ];
    }
}
