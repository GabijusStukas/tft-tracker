<?php

namespace App\Services\Riot;

use App\DTO\SummonerSearchDTO;
use App\Models\Summoner;
use App\Services\Riot\API\RiotServiceFactory;
use GuzzleHttp\Exception\GuzzleException;

class SummonerService
{
    /**
     * Get summoner information by summoner name
     *
     * @param SummonerSearchDTO $DTO
     * @return Summoner
     * @throws GuzzleException
     */
    public function getSummonerByName(SummonerSearchDTO $DTO): Summoner
    {
        $summoner = Summoner::query()
            ->where('game_name', $DTO->getUsername())
            ->where('tag_line', $DTO->getTagLine())
            ->first();

        if (!$summoner) {
            $summonerData = RiotServiceFactory::account()->getSummonerByName($DTO->getUsername(), $DTO->getTagLine());
            $summoner = Summoner::query()->create([
                'game_name' => $summonerData['gameName'],
                'tag_line'  => $summonerData['tagLine'],
                'puuid'     => $summonerData['puuid'],
            ]);
        }

        return $summoner;
    }
}
