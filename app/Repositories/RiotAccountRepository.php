<?php

namespace App\Repositories;

use App\DTO\RiotAccountSearchDTO;
use App\Models\RiotAccount;

class RiotAccountRepository
{
    /**
     * @param RiotAccountSearchDTO $DTO
     * @return RiotAccount|null
     */
    public function getAccount(RiotAccountSearchDTO $DTO): ?RiotAccount
    {
        return RiotAccount::query()
            ->where('game_name', $DTO->getUsername())
            ->where('tag_line', $DTO->getTagLine())
            ->first();
    }

    /**
     * @param array $data
     * @return RiotAccount
     */
    public function createOrUpdateAccount(array $data): RiotAccount
    {
        return RiotAccount::query()->updateOrCreate([
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
