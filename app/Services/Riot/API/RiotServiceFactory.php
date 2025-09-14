<?php

namespace App\Services\Riot\API;

class RiotServiceFactory
{
    /**
     * @param RiotClient $client
     */
    public function __construct(private RiotClient $client)
    {
    }

    /**
     * @param string|null $region
     * @return AccountService
     */
    public function account(?string $region = null): AccountService
    {
        return new AccountService($this->client->setUpClient($region));
    }

    /**
     * @param string|null $region
     * @return TFTService
     */
    public function tft(?string $region = null): TFTService
    {
        return new TFTService($this->client->setUpClient($region));
    }
}
