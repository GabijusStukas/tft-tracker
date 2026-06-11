<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $participant_id
 * @property string $character_id
 * @property string $name
 * @property int $tier
 * @property int $rarity
 * @property string $icon
 * @property array $items
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RiotMatchParticipant $participant
 */
class RiotMatchParticipantUnit extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_match_participant_units';

    /**
     * @var string[]
     */
    protected $fillable = [
        'participant_id',
        'character_id',
        'name',
        'tier',
        'rarity',
        'icon',
        'items',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'items' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function participant(): BelongsTo
    {
        return $this->belongsTo(RiotMatchParticipant::class, 'participant_id', 'id');
    }
}

