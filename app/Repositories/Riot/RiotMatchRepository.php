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
     * @param string $puuid
     * @return Collection<int, RiotMatch>
     */
    public function getMatchesByPuuid(string $puuid): Collection
    {
        return RiotMatch::query()
            ->with(['participants' => function ($query) use ($puuid) {
                $query
                    ->where('puuid', $puuid)
                    ->with(['units', 'traits']);
            }])
            ->limit(5)
            ->latest('match_created_at')
            ->get();
    }

    /**
     * @param array $matchIds
     * @return array
     */
    public function getMissingMatchIds(array $matchIds): array
    {
        $existingMatches = RiotMatch::query()
            ->whereIn('match_id', $matchIds)
            ->pluck('match_id')
            ->toArray();

        return array_diff($matchIds, $existingMatches);
    }
}
