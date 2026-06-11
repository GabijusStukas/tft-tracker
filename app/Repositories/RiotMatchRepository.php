<?php

namespace App\Repositories;

use App\Models\Riot\RiotMatch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class RiotMatchRepository
{
    /**
     * @param array $matches
     * @return Collection
     */
    public function upsert(array $matches): Collection
    {
        RiotMatch::query()->upsert($matches, ['account_id', 'match_id']);

        $matchesByAccount = collect($matches)
            ->groupBy('account_id')
            ->map(fn ($accountMatches) => $accountMatches->pluck('match_id')->unique()->values());

        return RiotMatch::query()
            ->where(function (Builder $query) use ($matchesByAccount) {
                foreach ($matchesByAccount as $accountId => $matchIds) {
                    $query->orWhere(function (Builder $accountQuery) use ($accountId, $matchIds) {
                        $accountQuery
                            ->where('account_id', (int) $accountId)
                            ->whereIn('match_id', $matchIds->all());
                    });
                }
            })
            ->limit(10)
            ->latest('match_created_at')
            ->get();
    }

    /**
     * @param int $accountId
     * @return Collection
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
