<?php

use Illuminate\Support\Facades\Route;
use App\Models\Recipe;

use App\Http\Controllers\nutritionApiController;
use App\Http\Controllers\recipeApiController;

Route::get('/add2Ings', function () { //adding 2 ingredients with random value
    $result = nutritionApiController::addIngredient("Ananas",1.5,6.7,4) 
            && nutritionApiController::addIngredient("Chocolate",2,5.2,3);
    dd($result);
});
