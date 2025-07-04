<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
protected $table = "ingredients";
    protected $fillable = [
        'recipe_id',
        'ingredient',
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
