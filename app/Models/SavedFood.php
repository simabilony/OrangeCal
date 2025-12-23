<?php
// app/Models/SavedFood.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedFood extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'food_id',
        'notes',
        'custom_name',
        'default_quantity',
        'default_unit',
    ];

    protected $casts = [
        'default_quantity' => 'decimal:2',
    ];

    // ==================== العلاقات ====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    // ==================== Helper Methods ====================

    public function getDisplayName(string $locale = 'ar'): string
    {
        return $this->custom_name ?? $this->food->getLocalizedName($locale);
    }
}
