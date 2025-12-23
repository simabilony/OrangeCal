<?php
// app/Models/UserMeal.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMeal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'arabic_name',
        'english_name',
        'description',
        'total_calories',
        'total_protein',
        'total_carbs',
        'total_fats',
        'meal_type',
        'servings',
        'prep_time',
        'instructions',
        'image_url',
        'is_favorite',
        'is_public',
    ];

    protected $casts = [
        'total_calories' => 'decimal:2',
        'total_protein' => 'decimal:2',
        'total_carbs' => 'decimal:2',
        'total_fats' => 'decimal:2',
        'is_favorite' => 'boolean',
        'is_public' => 'boolean',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ingredients()
    {
        return $this->hasMany(MealIngredient::class);
    }

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'meal_ingredients')
            ->withPivot(['quantity', 'unit', 'grams', 'calories', 'protein', 'carbs', 'fats', 'notes'])
            ->withTimestamps();
    }

    public function foodLogs()
    {
        return $this->hasMany(FoodLog::class);
    }

    // ==================== Scopes ====================

    public function scopeFavorite($query)
    {
        return $query->where('is_favorite', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // ==================== Helper Methods ====================

    public function recalculateTotals(): void
    {
        $totals = $this->ingredients()->selectRaw('
            SUM(calories) as total_calories,
            SUM(protein) as total_protein,
            SUM(carbs) as total_carbs,
            SUM(fats) as total_fats
        ')->first();

        $this->update([
            'total_calories' => $totals->total_calories ?? 0,
            'total_protein' => $totals->total_protein ?? 0,
            'total_carbs' => $totals->total_carbs ?? 0,
            'total_fats' => $totals->total_fats ?? 0,
        ]);
    }

    public function getLocalizedName(string $locale = 'ar'): string
    {
        return $locale === 'en'
            ? ($this->english_name ?? $this->name)
            : ($this->arabic_name ?? $this->name);
    }

    public function duplicate(): self
    {
        $newMeal = $this->replicate();
        $newMeal->name = $this->name . ' (نسخة)';
        $newMeal->save();

        foreach ($this->ingredients as $ingredient) {
            $newMeal->ingredients()->create($ingredient->toArray());
        }

        return $newMeal;
    }
}
