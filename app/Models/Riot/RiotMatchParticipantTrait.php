<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $participant_id
 * @property string $trait_id
 * @property string $name
 * @property int $style
 * @property int $num_units
 * @property int $tier_total
 * @property int $tier_current
 * @property string $icon
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RiotMatchParticipant $participant
 */
class RiotMatchParticipantTrait extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_match_participant_traits';

    /**
     * @var string[]
     */
    protected $fillable = [
        'participant_id',
        'trait_id',
        'name',
        'style',
        'num_units',
        'tier_total',
        'tier_current',
        'icon',
    ];

    /**
     * @return BelongsTo
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(RiotMatchParticipant::class, 'participant_id', 'id');
    }
}

