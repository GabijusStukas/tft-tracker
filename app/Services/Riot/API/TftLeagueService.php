<?php

namespace App\Services\Riot\API;

use App\Http\Exceptions\RiotApiException;
use Illuminate\Support\Facades\Log;
use Throwable;

class TftLeagueService
{
    /**
     * @param RiotClient $client
     */
    public function __construct(private RiotClient $client)
    {
    }

    /**
     * @param string $puuid
     * @return array
     * @throws RiotApiException
     */
    public function getLeagues(string $puuid): array
    {
        try {
            return $this->client->request('GET',"tft/league/v1/by-puuid/$puuid");
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message' => $exception->getMessage(),
                'puuid'   => $puuid
            ]);
            throw new RiotApiException('Could not get TFT league details', 404);
        }
    }
}
