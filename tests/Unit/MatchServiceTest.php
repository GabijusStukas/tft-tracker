<?php

namespace Tests\Unit;

use App\Enums\DataDragonIconType;
use App\Models\Riot\RiotMatch;
use App\Models\Riot\RiotMatchParticipant;
use App\Models\Riot\RiotMatchParticipantTrait;
use App\Models\Riot\RiotMatchParticipantUnit;
use App\Repositories\Riot\RiotMatchParticipantRepository;
use App\Repositories\Riot\RiotMatchParticipantTraitRepository;
use App\Repositories\Riot\RiotMatchParticipantUnitRepository;
use App\Repositories\Riot\RiotMatchRepository;
use App\Repositories\Riot\RiotRepositoryFactory;
use App\Services\Riot\DataDragonService;
use App\Services\Riot\MatchService;
use Mockery;
use Tests\TestCase;

class MatchServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_replaces_participant_children_when_syncing_the_same_match_again(): void
    {
        $dataDragonService = Mockery::mock(DataDragonService::class);

        $dataDragonService->shouldReceive('getVersion')
            ->andReturn('15.11.1');

        $dataDragonService->shouldReceive('getCdnData')
            ->andReturn([
                'data' => [
                    1100 => ['name' => 'Ranked'],
                ],
            ]);

        $dataDragonService->shouldReceive('getEntityCdnData')
            ->andReturnUsing(function (string $entityId, DataDragonIconType $iconType): ?array {
                return [
                    'name' => sprintf('%s-%s', $iconType->value, $entityId),
                ];
            });

        $dataDragonService->shouldReceive('getCdnImageUrl')
            ->andReturnUsing(function (string $entityId, DataDragonIconType $iconType): string {
                return sprintf('/storage/%s/%s.png', $iconType->value, $entityId);
            });

        $service = new MatchService(
            new RiotRepositoryFactory(
                app()->make(\App\Repositories\Riot\RiotAccountRepository::class),
                app()->make(\App\Repositories\Riot\RiotRegionRepository::class),
                new RiotMatchRepository(),
                app()->make(\App\Repositories\Riot\RiotSummonerRepository::class),
                app()->make(\App\Repositories\Riot\RiotLeagueRepository::class),
                new RiotMatchParticipantRepository(),
                new RiotMatchParticipantTraitRepository(),
                new RiotMatchParticipantUnitRepository(),
            ),
            $dataDragonService
        );

        $matchData = [
            'metadata' => [
                'match_id' => 'EUW1_12345',
            ],
            'info' => [
                'game_version' => 'Version <Releases/15.11> [PUBLIC]',
                'queueId' => 1100,
                'tft_set_number' => 14,
                'gameCreation' => now()->valueOf(),
                'participants' => [
                    [
                        'puuid' => 'player-1',
                        'riotIdGameName' => 'PlayerOne',
                        'riotIdTagline' => 'EUW',
                        'level' => 8,
                        'gold_left' => 3,
                        'placement' => 1,
                        'last_round' => 34,
                        'units' => [
                            [
                                'character_id' => 'TFT14_ExampleChampion',
                                'tier' => 2,
                                'rarity' => 1,
                                'itemNames' => [
                                    'TFT_Item_Example',
                                ],
                            ],
                        ],
                        'traits' => [
                            [
                                'name' => 'TFT14_ExampleTrait',
                                'style' => 2,
                                'num_units' => 4,
                                'tier_total' => 4,
                                'tier_current' => 2,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $service->parseMatchData($matchData);
        $service->parseMatchData($matchData);

        $match = RiotMatch::query()->where('match_id', 'EUW1_12345')->firstOrFail();
        $participant = RiotMatchParticipant::query()
            ->where('match_id', $match->id)
            ->where('puuid', 'player-1')
            ->firstOrFail();

        $this->assertSame(1, RiotMatch::query()->where('match_id', 'EUW1_12345')->count());
        $this->assertSame(
            1,
            RiotMatchParticipant::query()->where('match_id', $match->id)->where('puuid', 'player-1')->count()
        );
        $this->assertSame(1, RiotMatchParticipantUnit::query()->where('participant_id', $participant->id)->count());
        $this->assertSame(1, RiotMatchParticipantTrait::query()->where('participant_id', $participant->id)->count());

        $unit = RiotMatchParticipantUnit::query()->where('participant_id', $participant->id)->firstOrFail();
        $this->assertSame('tft-item-TFT_Item_Example', $unit->items[0]['name']);
        $this->assertSame('/storage/tft-item/TFT_Item_Example.png', $unit->items[0]['icon']);
    }
}

