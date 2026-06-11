<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotLeague;
use App\Models\Riot\RiotSummoner;
use Illuminate\Support\Collection;

class RiotLeagueRepository
{
    /**
     * @param array $data
     * @return RiotLeague
     */
    public function createOrUpdate(array $data): RiotLeague
    {
        return RiotLeague::query()->updateOrCreate([
            'account_id' => $data['account_id'],
            'queue_type' => $data['queue_type'],
        ], $data);
    }

    /**
     * @param int $accountId
     * @return Collection<int, RiotLeague>
     */
    public function getByAccountId(int $accountId): Collection
    {
        return RiotLeague::query()->where('account_id', $accountId)->get();
    }
}
