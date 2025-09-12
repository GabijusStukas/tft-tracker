<?php

namespace App\Services\Riot\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use InvalidArgumentException;

class RiotClient
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @param string|null $region
     * @return RiotClient
     */
    public function setUpClient(?string $region = null): self
    {
        if (empty(config('services.riot.api_key'))) {
            throw new InvalidArgumentException('Riot API key is not configured');
        }

        $this->client = new Client([
            'base_uri' => sprintf(config('services.riot.api_url'), $region ?? config('services.riot.default_region')),
            'headers'  => [
                'X-Riot-Token' => config('services.riot.api_key'),
                'Accept'       => 'application/json',
            ],
        ]);

        return $this;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return array
     * @throws GuzzleException
     */
    public function request(string $method, string $uri, array $options = []): array
    {
        $response = $this->client->request($method, $uri, $options);
        return json_decode($response->getBody()->getContents(), true);
    }
}
