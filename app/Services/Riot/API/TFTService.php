<?php

namespace App\Services\Riot\API;

use App\Http\Exceptions\RiotApiException;
use Illuminate\Support\Facades\Log;
use Throwable;

class TFTService
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
    public function getMatchesByPuuid(string $puuid): array
    {
        try {
            return $this->client->request('GET',"tft/match/v1/matches/by-puuid/$puuid/ids");
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message'  => $exception->getMessage(),
                'puuid'    => $puuid
            ]);
            throw new RiotApiException('Could not get matches for summoner', 404);
        }
    }
}
