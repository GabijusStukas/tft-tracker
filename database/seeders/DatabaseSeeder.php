<?php

namespace Database\Seeders;

use App\Models\RiotRegion;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        RiotRegion::factory()->create([
            'cluster' => 'europe',
            'region'  => 'euw1',
        ]);

        RiotRegion::factory()->create([
            'cluster' => 'europe',
            'region'  => 'eun1',
        ]);
    }
}
