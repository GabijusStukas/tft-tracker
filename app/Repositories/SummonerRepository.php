<?php

namespace App\Repositories;

use App\DTO\SummonerSearchDTO;
use App\Models\Summoner;

class SummonerRepository
{
    /**
     * @param SummonerSearchDTO $DTO
     * @return Summoner|null
     */
    public function getSummoner(SummonerSearchDTO $DTO): ?Summoner
    {
        return Summoner::query()
            ->where('game_name', $DTO->getUsername())
            ->where('tag_line', $DTO->getTagLine())
            ->first();
    }

    /**
     * @param array $data
     * @return Summoner
     */
    public function createOrUpdateSummoner(array $data): Summoner
    {
        return Summoner::query()->updateOrCreate([
            'game_name' => $data['gameName'],
            'tag_line'  => $data['tagLine'],
        ], [
            'game_name' => $data['gameName'],
            'tag_line'  => $data['tagLine'],
            'puuid'     => $data['puuid'],
            'game'      => $data['game'],
            'region'    => $data['region']
        ]);
    }
}
