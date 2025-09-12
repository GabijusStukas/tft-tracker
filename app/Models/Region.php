<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $cluster
 * @property string $region
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Region extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'regions';

    /**
     * @var string[]
     */
    protected $fillable = [
        'cluster',
        'region',
    ];
}
