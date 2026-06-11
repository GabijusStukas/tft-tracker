<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotSummoner;

class RiotSummonerRepository
{
    /**
     * @param RiotAccount $account
     * @param array $data
     * @return RiotSummoner
     */
    public function createOrUpdate(RiotAccount $account, array $data): RiotSummoner
    {
        return RiotSummoner::query()->updateOrCreate([
            'account_id' => $account->id,
        ], [
            'profile_icon_id' => $data['profileIconId'],
            'summoner_level'  => $data['summonerLevel'],
        ]);
    }
}
