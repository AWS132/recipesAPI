<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\Recipe;
use App\Models\Ingredient;

class recipeApiController //Controller for recipe api
{
    public static function addRecipe($name, $steps = null, $ingredients = null)
    {
        $validator = Validator::make([
            'name' => $name
        ], [
            'name' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ];
        }

        $recipe = Recipe::create([
            'name' => $name,
            'steps' => $steps,
            'total_carbs' => 0,
            'total_fat' => 0,
            'total_protein' => 0
        ]);

        // Add ingredients if provided
        if ($ingredients && is_array($ingredients)) {
            foreach ($ingredients as $item) {
                $ingredientName = $item['ingredient'];
                $quantity = (float)$item['quantity'];

                Ingredient::create([
                    'recipe_id' => $recipe->id,
                    'ingredient' => $ingredientName,
                    'quantity' => $quantity
                ]);
            }
        }

        // Recalculate nutritional values
        $totals = self::recalculateNutritionalVal($recipe->id);

        $recipe->update([
            'total_protein' => $totals['protein'],
            'total_fat' => $totals['fat'],
            'total_carbs' => $totals['carbs']
        ]);

        return [
            'success' => true,
            'recipe' => $recipe,
            'message' => 'Recipe created successfully'
        ];
    }

    public static function addIngredient($name, $recipeId, $quantity)
    {
        $validator = Validator::make([
            'name' => $name,
            'recipeId' => $recipeId,
            'quantity' => $quantity
        ], [
            'name' => 'required|string|max:255',
            'recipeId' => 'required|integer|exists:recipes,id',
            'quantity' => 'required|numeric|min:0.25'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ];
        }

        // Check if ingredient already exists for this recipe
        $existingIngredient = Ingredient::where('recipe_id', $recipeId)
            ->where('ingredient', $name)
            ->first();

        if ($existingIngredient) {
            // Update quantity if ingredient exists
            $existingIngredient->update(['quantity' => $quantity]);
            $ingredient = $existingIngredient;
        } else {
            // Create new ingredient
            $ingredient = Ingredient::create([
                'recipe_id' => $recipeId,
                'ingredient' => $name,
                'quantity' => $quantity
            ]);
        }

        // Recalculate nutritional values
        $totals = self::recalculateNutritionalVal($recipeId);

        Recipe::where('id', $recipeId)->update([
            'total_protein' => $totals['protein'],
            'total_fat' => $totals['fat'],
            'total_carbs' => $totals['carbs']
        ]);

        return [
            'success' => true,
            'ingredient' => $ingredient,
            'message' => 'Ingredient added successfully'
        ];
    }
    public static function removeIngredient($name, $recipeId)
    {
        $validator = Validator::make([
            'name' => $name,
            'recipeId' => $recipeId
        ], [
            'name' => 'required|string|max:255',
            'recipeId' => 'required|integer|exists:recipes,id'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ];
        }

        $ingredient = Ingredient::where('recipe_id', $recipeId)
            ->where('ingredient', $name)
            ->first();

        if (!$ingredient) {
            return [
                'success' => false,
                'error' => 'this ingredient is not in this recipe'
            ];
        }

        $ingredient->delete();

        // Recalculate nutritional values
        $totals = self::recalculateNutritionalVal($recipeId);

        Recipe::where('id', $recipeId)->update([
            'total_protein' => $totals['protein'],
            'total_fat' => $totals['fat'],
            'total_carbs' => $totals['carbs']
        ]);

        return [
            'success' => true,
            'message' => 'Ingredient removed successfully'
        ];
    }
    public static function updateSteps($steps, $recipeId)
    {
        $validator = Validator::make([
            'steps' => $steps,
            'recipeId' => $recipeId
        ], [
            'steps' => 'required|string',
            'recipeId' => 'required|integer|exists:recipes,id'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ];
        }

        $recipe = Recipe::find($recipeId);
        $recipe->update(['steps' => $steps]);

        return [
            'success' => true,
            'recipe' => $recipe,
            'message' => 'Recipe steps updated successfully'
        ];
    }

    protected static function recalculateNutritionalVal($id)
    {
        $ingredients = Ingredient::where('recipe_id', $id)->get();

        $totalProtein = 0;
        $totalFat = 0;
        $totalCarbs = 0;

        foreach ($ingredients as $ingredient) {
            $nutritionData = nutritionApiController::getNutritionalVal($ingredient->ingredient);
            if (isset($nutritionData['protein']) && isset($nutritionData['fat']) && isset($nutritionData['carbs'])) {
                // Calculate nutritional values based on quantity
                $totalProtein += $nutritionData['protein'] * $ingredient->quantity;
                $totalFat += $nutritionData['fat'] * $ingredient->quantity;
                $totalCarbs += $nutritionData['carbs'] * $ingredient->quantity;
            }
        }

        return [
            'protein' => round($totalProtein, 2),
            'fat' => round($totalFat, 2),
            'carbs' => round($totalCarbs, 2)
        ];
    }
    public static function deleteRecipe($id)
    {
        $validator = Validator::make([
            'id' => $id
        ], [
            'id' => 'required|integer|exists:recipes,id'
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'error' => 'Validation failed',
                'details' => $validator->errors()
            ];
        }

        $recipe = Recipe::find($id);

        // Delete the recipe
        $recipe->delete();

        return [
            'success' => true,
            'message' => 'Recipe deleted successfully'
        ];
    }

    public static function getTotalNutritionalVal($method, $attr) //getting the nutritional value by either the name or id depending on $method var
    {
        $recipe = Recipe::where($method, $attr)->first();

        if (!$recipe) {
            return response()->json(['error' => 'Recipe not found'], 404);
        }

        return response()->json([
            'name' => $recipe->name,
            'total_carbs' => $recipe->total_carbs,
            'total_fat' => $recipe->total_fat,
            'total_protein' => $recipe->total_protein,
        ]);
    }
}
