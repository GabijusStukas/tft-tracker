<?php

namespace Tests\Unit;

use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotMatch;
use App\Models\Riot\RiotRegion;
use App\Repositories\RiotMatchRepository;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Tests\TestCase;

class RiotMatchRepositoryTest extends TestCase
{
    public function test_it_upserts_matches_and_returns_riot_match_models(): void
    {
        $region = RiotRegion::query()->first();

        $account = RiotAccount::factory()->create([
            'region' => $region->region,
        ]);

        $repository = new RiotMatchRepository();

        $matches = [
            [
                'account_id' => $account->id,
                'match_id' => 'EUW1_1001',
                'raw_data' => json_encode(['placement' => 1]),
                'game_version' => '10.24.1',
                'match_created_at' => now(),
            ],
            [
                'account_id' => $account->id,
                'match_id' => 'EUW1_1002',
                'raw_data' => json_encode(['placement' => 2]),
                'game_version' => '10.24.1',
                'match_created_at' => now(),
            ],
        ];

        $result = $repository->upsert($matches);

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertContainsOnlyInstancesOf(RiotMatch::class, $result);
        $this->assertCount(2, $result);
        $this->assertEqualsCanonicalizing(
            ['EUW1_1001', 'EUW1_1002'],
            $result->pluck('match_id')->all()
        );
        $this->assertSame(
            ['placement' => 1],
            $result->firstWhere('match_id', 'EUW1_1001')?->raw_data
        );
    }

    public function test_it_returns_an_empty_eloquent_collection_when_no_matches_are_provided(): void
    {
        $repository = new RiotMatchRepository();

        $result = $repository->upsert([]);

        $this->assertInstanceOf(EloquentCollection::class, $result);
        $this->assertTrue($result->isEmpty());
    }
}

