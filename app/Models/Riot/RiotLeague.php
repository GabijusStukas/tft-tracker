<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $queue_type
 * @property string $tier
 * @property string $rank
 * @property int $league_points
 * @property int $wins
 * @property int $losses
 * @property RiotAccount $account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RiotLeague extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_leagues';

    /**
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'queue_type',
        'tier',
        'rank',
        'league_points',
        'wins',
        'losses',
    ];

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(RiotAccount::class, 'account_id');
    }
}
