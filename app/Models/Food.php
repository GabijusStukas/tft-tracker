<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property float|null $calories
 * @property float|null $protein
 * @property float|null $carbs
 * @property float|null $fat
 * @property string|null $image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Food extends Model
{
    protected $table = 'foods';

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'calories',
        'protein',
        'carbs',
        'fat',
        'image',
    ];
}
