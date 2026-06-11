<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $match_id
 * @property string $game_version
 * @property array $raw_data
 * @property Carbon|null $match_created_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property RiotAccount $account
 */
class RiotMatch extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_matches';

    /**
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'match_id',
        'game_version',
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
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(RiotAccount::class, 'account_id');
    }
}
