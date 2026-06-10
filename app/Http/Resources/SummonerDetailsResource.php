<?php

namespace App\Http\Resources;

use App\Services\Riot\DataDragonService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SummonerDetailsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     * @throws GuzzleException
     */
    public function toArray(Request $request): array
    {
        $dataDragonService = app(DataDragonService::class);

        return [
            'profile_icon_url' => $dataDragonService->getSummonerIconUrl((int) $this->profile_icon_id),
            'summoner_level' => $this->summoner_level,
        ];
    }
}
