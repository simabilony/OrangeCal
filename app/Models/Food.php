<?php
// app/Models/Food.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Traits\CalculatesNutrition;

class Food extends Model
{
    use HasFactory, HasTranslations, CalculatesNutrition;

    protected $table = 'food';

    public $translatable = ['name', 'description', 'category'];

    protected $fillable = [
        'name',
        'description',
        'category',
        'barcode',
        'source',
        'calories',
        'protein',
        'carbs',
        'fats',
        'fiber',
        'sugar',
        'sodium',
        'saturated_fat',
        'cholesterol',
        'serving_size',
        'serving_unit',
        'is_halal',
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'is_verified',
        'image_url',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'protein' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
        'fiber' => 'decimal:2',
        'sugar' => 'decimal:2',
        'sodium' => 'decimal:2',
        'saturated_fat' => 'decimal:2',
        'cholesterol' => 'decimal:2',
        'serving_size' => 'decimal:2',
        'is_halal' => 'boolean',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
        'is_verified' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'saved_foods')
            ->withPivot(['notes', 'custom_name', 'default_quantity', 'default_unit'])
            ->withTimestamps();
    }

    public function mealIngredients()
    {
        return $this->hasMany(MealIngredient::class);
    }

    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }

    // ==================== Scopes ====================

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeHalal($query)
    {
        return $query->where('is_halal', true);
    }

    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->whereRaw('JSON_EXTRACT(name, "$.ar") LIKE ?', ["%{$term}%"])
              ->orWhereRaw('JSON_EXTRACT(name, "$.en") LIKE ?', ["%{$term}%"]);
        });
    }

}
