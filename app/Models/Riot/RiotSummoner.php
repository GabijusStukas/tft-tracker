<?php

namespace App\Models\Riot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property int $summoner_level
 * @property int $profile_icon_id
 * @property RiotAccount $account
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class RiotSummoner extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'riot_summoners';

    /**
     * @var string[]
     */
    protected $fillable = [
        'account_id',
        'summoner_level',
        'profile_icon_id',
        'queue_type',
    ];

    /**
     * @return BelongsTo
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(RiotAccount::class, 'account_id', 'id');
    }
}
