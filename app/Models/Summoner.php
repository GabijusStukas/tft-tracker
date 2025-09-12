<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $puuid
 * @property string $game_name
 * @property string $tag_line
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Summoner extends Model
{
    /**
     * @var string
     */
    protected $table = 'summoners';

    /**
     * @var string[]
     */
    protected $fillable = [
        'puuid',
        'game_name',
        'tag_line',
    ];
}
