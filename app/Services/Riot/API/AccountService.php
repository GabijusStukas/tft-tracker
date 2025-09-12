<?php

namespace App\Services\Riot\API;

use App\Http\Exceptions\RiotApiException;
use Illuminate\Support\Facades\Log;
use Throwable;

class AccountService
{
    /**
     * @param RiotClient $client
     */
    public function __construct(private RiotClient $client)
    {
    }

    /**
     * @param string $username
     * @param string|null $tagLine
     * @return array
     * @throws RiotApiException
     */
    public function getSummonerByName(string $username, ?string $tagLine): array
    {
        try {
            return $this->client->request('GET',"riot/account/v1/accounts/by-riot-id/{$username}/{$tagLine}");
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message'  => $exception->getMessage(),
                'username' => $username,
                'tagLine'  => $tagLine
            ]);
            throw new RiotApiException('Could not find such summoner: '.$username.'#'.$tagLine, 404);
        }
    }

    /**
     * @param string $puuid
     * @param string $game
     * @return array
     * @throws RiotApiException
     */
    public function getRegionByGame(string $puuid, string $game = 'tft'): array
    {
        try {
            return $this->client->request('GET',"riot/account/v1/region/by-game/$game/by-puuid/$puuid");
        } catch (Throwable $exception) {
            Log::info('Riot API error', [
                'message' => $exception->getMessage(),
                'puuid'   => $puuid,
                'game'    => $game
            ]);
            throw new RiotApiException('Could not fetch region', 404);
        }
    }
}
