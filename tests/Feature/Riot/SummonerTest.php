<?php

namespace Tests\Feature\Riot;

use App\Http\Exceptions\RiotApiException;
use App\Models\Summoner;
use App\Services\Riot\API\RiotClient;
use Tests\TestCase;
use Mockery;

class SummonerTest extends TestCase
{
    /**
     * @var RiotClient|Mockery\MockInterface $riotClient
     */
    private RiotClient|Mockery\MockInterface $riotClient;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->riotClient = Mockery::mock(RiotClient::class);
    }
    /**
     * @return void
     */
    public function testCanSearchSummonerSuccessfully(): void
    {
        $this->riotClient->shouldReceive('setUpClient')
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('request')
            ->once()
            ->with('GET', 'riot/account/v1/accounts/by-riot-id/TestUser/EUW')
            ->andReturn([
                'gameName' => 'TestUser',
                'tagLine' => 'EUW',
                'puuid' => 'test-puuid'
            ]);

        $this->app->instance(RiotClient::class, $this->riotClient);

        $response = $this->getJson(route('summoner.search', [
            'username' => 'TestUser',
            'tag_line' => 'EUW',
            'region' => 'euw'
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'game_name' => 'TestUser',
                'tag_line' => 'EUW',
                'puuid' => 'test-puuid'
            ]);

        $this->assertDatabaseHas('summoners', [
            'game_name' => 'TestUser',
            'tag_line' => 'EUW',
            'puuid' => 'test-puuid'
        ]);
    }

    /**
     * @return void
     */
    public function testReturnsErrorWhenSummonerNotFound(): void
    {
        $this->riotClient->shouldReceive('setUpClient')
            ->once()
            ->andReturnSelf()
            ->getMock()
            ->shouldReceive('request')
            ->once()
            ->with('GET', 'riot/account/v1/accounts/by-riot-id/NonExistentUser/EUW')
            ->andThrow(new RiotApiException('Could not find such summoner: NonExistentUser#EUW', 404));

        $this->app->instance(RiotClient::class, $this->riotClient);

        $response = $this->getJson(route('summoner.search', [
            'username' => 'NonExistentUser',
            'tag_line' => 'EUW',
            'region' => 'euw'
        ]));

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Could not find such summoner: NonExistentUser#EUW'
            ]);

        $this->assertDatabaseMissing('summoners', [
            'game_name' => 'NonExistentUser',
            'tag_line' => 'EUW'
        ]);
    }

    /**
     * @return void
     */
    public function testRiotApiNotCalledIfSummonerExistsInDatabase(): void
    {
        /** @var Summoner $summoner */
        $summoner = Summoner::factory()->create();

        $this->riotClient->shouldNotReceive('setUpClient')
            ->getMock()
            ->shouldNotReceive('request');

        $this->app->instance(RiotClient::class, $this->riotClient);

        $response = $this->getJson(route('summoner.search', [
            'username' => $summoner->game_name,
            'tag_line' => $summoner->tag_line,
            'region' => 'na'
        ]));

        $response->assertStatus(200)
            ->assertJson([
                'game_name' => $summoner->game_name,
                'tag_line' => $summoner->tag_line,
                'puuid' => $summoner->puuid
            ]);

        $this->assertDatabaseHas('summoners', [
            'game_name' => $summoner->game_name,
            'tag_line' => $summoner->tag_line,
            'puuid' => $summoner->puuid
        ]);
    }
}
