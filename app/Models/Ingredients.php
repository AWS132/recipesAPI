<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{

    protected $fillable = [
        'recipe_id',
        'ingredient_name',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
