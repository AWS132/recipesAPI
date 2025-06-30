<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Steps extends Model
{
     protected $fillable = [
        'recipe_id',
        'description',
        'order'
    ];

    protected $casts = [
        'order' => 'integer'
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }
}
