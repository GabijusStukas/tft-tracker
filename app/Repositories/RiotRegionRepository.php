<?php

namespace App\Repositories;

use App\Models\RiotRegion;
use Illuminate\Support\Facades\Cache;

class RiotRegionRepository
{
    /**
     * @param string $region
     * @return string
     */
    public function getClusterByRegion(string $region): string
    {
        return Cache::remember("region_$region", 3600, function () use ($region) {
            $riotRegion = RiotRegion::query()
                ->where('region', $region)
                ->first();

            return $riotRegion->cluster ?? config('services.riot.default_region');
        });
    }
}
