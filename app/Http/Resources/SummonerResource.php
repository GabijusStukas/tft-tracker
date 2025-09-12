<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummonerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'puuid'     => $this->puuid,
            'game_name' => $this->game_name,
            'tag_line'  => $this->tag_line,
            'game'      => $this->game,
            'region'    => $this->region,
        ];
    }
}
