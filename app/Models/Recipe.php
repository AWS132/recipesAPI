<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = "recipe";
    protected $fillable = [
        'name',
        'total_carbs',
        'total_fat',
        'total_protein',
        'steps'
    ];

    protected $casts = [
        'total_carbs' => 'float',
        'total_fat' => 'float',
        'total_protein' => 'float'
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredient::class);
    }
}
