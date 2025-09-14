<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $account_id
 * @property string $match_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
    ];
}
