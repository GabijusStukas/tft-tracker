<?php

namespace App\Repositories\Riot;

use App\Models\Riot\RiotMatch;
use Illuminate\Database\Eloquent\Collection;

class RiotMatchRepository
{
    /**
     * @param array $matches
     * @return RiotMatch
     */
    public function createOrUpdate(array $matches): RiotMatch
    {
        return RiotMatch::query()->updateOrCreate(
            ['match_id' => $matches['match_id']],
            $matches
        );
    }

    /**
     * @param int $accountId
     * @return Collection<int, RiotMatch>
     */
    public function getMatchesByAccountId(int $accountId): Collection
    {
        return RiotMatch::query()
            ->with('account')
            ->where('account_id', $accountId)
            ->limit(10)
            ->latest('match_created_at')
            ->get();
    }
}
