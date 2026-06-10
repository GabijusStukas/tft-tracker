<?php

namespace App\Services\Riot\API;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;

class DataDragonClient
{
    /**
     * @var Client
     */
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim((string) config('services.riot.datadragon_url'), '/') . '/',
            'timeout' => 5,
        ]);
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

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     * @throws GuzzleException
     */
    public function requestImage(string $method, string $uri, array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}

