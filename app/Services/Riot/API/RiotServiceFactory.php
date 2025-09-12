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
}
