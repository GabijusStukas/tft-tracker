<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RiotLeagueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fileName = strtolower($this->tier) . '.png';
        $iconPath = asset('img/riot/emblems/' . $fileName);

        return [
            'queue_type' => $this->queue_type,
            'tier' => $this->tier,
            'rank' => $this->rank,
            'league_points' => $this->league_points,
            'wins' => $this->wins,
            'losses' => $this->losses,
            'icon' => $iconPath
        ];
    }
}
