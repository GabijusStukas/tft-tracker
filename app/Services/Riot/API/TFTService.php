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
            return $this->client->request('GET',"tft/match/v1/matches/by-puuid/$puuid/ids", [
                'query' => [
                    'start' => 0,
                    'count' => 5
                ]
            ]);
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message' => $exception->getMessage(),
                'puuid'   => $puuid
            ]);
            throw new RiotApiException('Could not get matches for summoner', 404);
        }
    }

    /**
     * @param string $matchId
     * @return array
     * @throws RiotApiException
     */
    public function getMatch(string $matchId): array
    {
        try {
            return $this->client->request('GET',"tft/match/v1/matches/$matchId");
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message'  => $exception->getMessage(),
                'match_id' => $matchId
            ]);
            throw new RiotApiException('Could not get match', 404);
        }
    }
}
