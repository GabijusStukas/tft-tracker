<?php

namespace App\Repositories;

use App\Models\RiotAccount;
use App\Models\RiotMatch;
use Illuminate\Support\Collection;

class RiotMatchRepository
{
    /**
     * @param array $matches
     * @param RiotAccount $account
     * @return Collection
     */
    public function upsert(array $matches, RiotAccount $account): Collection
    {
        $records = collect($matches)->map(fn(string $matchId) => [
            'account_id' => $account->id,
            'match_id' => $matchId,
        ])->toArray();

        RiotMatch::query()->upsert($records, ['account_id', 'match_id']);

        return collect($matches);
    }
}
