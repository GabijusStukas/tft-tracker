<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int $id
 * @property int $match_id
 * @property string $puuid
 * @property string $game_name
 * @property string $tag_line
 * @property int $level
 * @property int $gold_left
 * @property int $placement
 * @property int $last_round
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RiotMatch $match
 * @property Collection<int, RiotMatchParticipantUnit> $units
 * @property Collection<int, RiotMatchParticipantTrait> $traits
 */
class RiotMatchParticipant extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_match_participants';

    /**
     * @var string[]
     */
    protected $fillable = [
        'match_id',
        'puuid',
        'game_name',
        'tag_line',
        'level',
        'gold_left',
        'placement',
        'last_round',
    ];

    /**
     * @return BelongsTo
     */
    public function match(): BelongsTo
    {
        return $this->belongsTo(RiotMatch::class, 'match_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function units(): HasMany
    {
        return $this->hasMany(RiotMatchParticipantUnit::class, 'participant_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function traits(): HasMany
    {
        return $this->hasMany(RiotMatchParticipantTrait::class, 'participant_id', 'id');
    }
}

