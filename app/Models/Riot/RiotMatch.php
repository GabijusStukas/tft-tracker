<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property string $match_id
 * @property string $game_version
 * @property string $queue_name
 * @property int $season
 * @property array $raw_data
 * @property Carbon|null $match_created_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RiotAccount $account
 * @property Collection<int, RiotMatchParticipant> $participants
 */
class RiotMatch extends Model
{
    use HasFactory;

    /** @var int */
    public const MAX_PARTICIPANTS = 8;

    /**
     * @var string
     */
    protected $table = 'riot_matches';

    /**
     * @var string[]
     */
    protected $fillable = [
        'match_id',
        'game_version',
        'queue_name',
        'season',
        'raw_data',
        'match_created_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'raw_data' => 'array',
        'match_created_at' => 'datetime',
    ];

    /**
     * @return HasMany
     */
    public function participants(): HasMany
    {
        return $this->hasMany(RiotMatchParticipant::class, 'match_id', 'id');
    }
}
