<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{

    protected $fillable = [
        'name',
        'total_carbs',
        'total_fat',
        'total_protein'
    ];

    protected $casts = [
        'total_carbs' => 'float',
        'total_fat' => 'float',
        'total_protein' => 'float'
    ];

    public function ingredients()
    {
        return $this->hasMany(Ingredients::class);
    }

    public function steps()
    {
        return $this->hasMany(Steps::class)->orderBy('order');
    }
}
