<?php

namespace App\Services\Riot\API;

class RiotServiceFactory
{
    /**
     * @param string|null $region
     * @return RiotClient
     */
    private static function createClient(?string $region = null): RiotClient
    {
        return new RiotClient($region ?? config('services.riot.default_region'));
    }

    /**
     * @param string|null $region
     * @return AccountService
     */
    public static function account(?string $region = null): AccountService
    {
        return new AccountService(self::createClient($region));
    }
}
