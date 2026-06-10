<?php

namespace Tests\Unit;

use App\Services\Riot\API\DataDragonClient;
use App\Services\Riot\DataDragonService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Tests\TestCase;

class DataDragonServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     * @throws GuzzleException
     */
    public function test_it_builds_summoner_icon_url_using_latest_version(): void
    {
        Cache::flush();
        Storage::fake('public');

        $client = Mockery::mock(DataDragonClient::class);
        $client->shouldReceive('request')
            ->once()
            ->with('GET', 'api/versions.json')
            ->andReturn(['15.12.1', '15.11.1']);
        $client->shouldReceive('requestImage')
            ->once()
            ->with('GET', 'cdn/15.12.1/img/profileicon/29.png', Mockery::on(fn (array $options) => ($options['headers']['Accept'] ?? null) === 'image/png'))
            ->andReturn(new Response(200, [], 'fake-png-content'));

        $service = new DataDragonService($client);

        $url = $service->getSummonerIconUrl(29);

        $this->assertSame(
            '/storage/riot/15.12.1/images/29.png',
            $url
        );

        Storage::disk('public')->assertExists('riot/15.12.1/images/29.png');
    }
}

