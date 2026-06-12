<?php

namespace Tests\Unit;

use App\Models\Riot\RiotAccount;
use App\Models\Riot\RiotMatch;
use App\Models\Riot\RiotRegion;
use App\Repositories\Riot\RiotMatchRepository;
use Tests\TestCase;

class RiotMatchRepositoryTest extends TestCase
{
    public function test_it_creates_a_match_with_create_or_update(): void
    {
        $region = RiotRegion::query()->first();

        $account = RiotAccount::factory()->create([
            'region' => $region->region,
        ]);

        $repository = new RiotMatchRepository();

        $result = $repository->createOrUpdate([
            'account_id' => $account->id,
            'match_id' => 'EUW1_1001',
            'raw_data' => ['placement' => 1],
            'queue_name' => 'ranked',
            'season' => 17,
            'game_version' => '10.24.1',
            'match_created_at' => now(),
        ]);

        $this->assertInstanceOf(RiotMatch::class, $result);
        $this->assertSame('EUW1_1001', $result->match_id);
        $this->assertSame(['placement' => 1], $result->raw_data);
    }

    public function test_it_updates_existing_match_with_create_or_update(): void
    {
        $region = RiotRegion::query()->first();

        $account = RiotAccount::factory()->create([
            'region' => $region->region,
        ]);

        $repository = new RiotMatchRepository();

        $created = $repository->createOrUpdate([
            'account_id' => $account->id,
            'match_id' => 'EUW1_2001',
            'raw_data' => ['placement' => 7],
            'queue_name' => 'ranked',
            'season' => 17,
            'game_version' => '10.24.1',
            'match_created_at' => now()->subDay(),
        ]);

        $updated = $repository->createOrUpdate([
            'account_id' => $account->id,
            'match_id' => 'EUW1_2001',
            'raw_data' => ['placement' => 3],
            'queue_name' => 'ranked',
            'season' => 17,
            'game_version' => '10.24.1',
            'match_created_at' => now(),
        ]);

        $this->assertSame($created->id, $updated->id);
        $this->assertSame(1, RiotMatch::query()->where('match_id', 'EUW1_2001')->count());
        $this->assertSame(['placement' => 3], $updated->fresh()?->raw_data);
    }
}

