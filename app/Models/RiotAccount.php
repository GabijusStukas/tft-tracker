<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $puuid
 * @property string $game_name
 * @property string $tag_line
 * @property string $game
 * @property string $region
 * @property RiotRegion $riotRegion
 * @property RiotSummoner $summoner
 * @property Collection<int, RiotMatch> $matches
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RiotAccount extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_accounts';

    /**
     * @var string[]
     */
    protected $fillable = [
        'puuid',
        'game_name',
        'tag_line',
        'game',
        'region',
    ];

    /**
     * @return HasOne
     */
    public function riotRegion(): HasOne
    {
        return $this->hasOne(RiotRegion::class, 'region', 'region');
    }

    /**
     * @return HasMany
     */
    public function matches(): HasMany
    {
        return $this->hasMany(RiotMatch::class, 'account_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function summoner(): HasOne
    {
        return $this->hasOne(RiotSummoner::class, 'account_id', 'id');
    }
}
