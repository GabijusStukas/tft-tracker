<?php

namespace App\Services\Riot\API;

use GuzzleHttp\Exception\GuzzleException;

class AccountService
{
    /**
     * @param RiotClient $client
     */
    public function __construct(private RiotClient $client)
    {
    }

    /**
     * @param string $username
     * @param string|null $tagLine
     * @return array
     * @throws GuzzleException
     */
    public function getSummonerByName( string $username, ?string $tagLine): array
    {
        return $this->client->request('GET',"riot/account/v1/accounts/by-riot-id/{$username}/{$tagLine}");
    }
}
