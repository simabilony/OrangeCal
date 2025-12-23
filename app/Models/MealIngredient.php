<?php
// app/Models/MealIngredient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_meal_id',
        'food_id',
        'quantity',
        'unit',
        'grams',
        'calories',
        'protein',
        'carbs',
        'fats',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'grams' => 'decimal:2',
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
    ];

    // ==================== العلاقات ====================

    public function meal()
    {
        return $this->belongsTo(UserMeal::class, 'user_meal_id');
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    // ==================== Helper Methods ====================

    public function calculateNutrition(): void
    {
        if ($this->food && $this->grams) {
            $nutrition = $this->food->calculateNutritionForGrams((float) $this->grams);

            $this->calories = $nutrition['calories'];
            $this->protein = $nutrition['protein'];
            $this->carbs = $nutrition['carbs'];
            $this->fats = $nutrition['fats'];
        }
    }

    protected static function booted()
    {
        static::saving(function ($ingredient) {
            $ingredient->calculateNutrition();
        });

        static::saved(function ($ingredient) {
            $ingredient->meal->recalculateTotals();
        });

        static::deleted(function ($ingredient) {
            $ingredient->meal->recalculateTotals();
        });
    }
}
