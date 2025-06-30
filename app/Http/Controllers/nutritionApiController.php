<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
class nutritionApiController //Controller for nutrition api
{
 public static function addIngredient( $name, $carbs, $fats, $protein) {
   $valid = Validator::make([
            'name' => $name,
            'carbs' => $carbs,
            'fat' => $fats,
            'protein' => $protein,
   ],
    [
            'name' => 'required|string|max:255',
            'carbs' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
   ]);
    if ($valid->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $valid->errors()
            ];
        }
    $response = Http::asForm()
                ->withBasicAuth('as', 'MAE298cPSN')
                ->post('https://interview.workcentrix.de/ingredients.php', [
                    'name' => $name,
                    'carbs' => $carbs,
                    'fat' => $fats,
                    'protein' => $protein
                ]);

                return $response->successful();
 }
public static function getNutritionalVal($name){
      $response = Http::withBasicAuth('as', 'MAE298cPSN')
        ->get('https://interview.workcentrix.de/ingredients.php', [
            'ingredient' => $name
        ]);
                 if($response->successful()){
                    return $response->json();
                 }
                 return [
                'status' => $response->status(),
                'message' => $response->body()
                 ];
 }

}