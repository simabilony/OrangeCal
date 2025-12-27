<?php
// database/migrations/2024_01_01_000006_create_meal_ingredients_table.php
// جدول مكونات الوجبات - الربط بين الوجبات والأطعمة

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_ingredients', function (Blueprint $table) {
            $table->id();

            // الربط بالوجبة والطعام
            $table->foreignId('user_meal_id')->constrained('user_meals')->onDelete('cascade');
            $table->foreignId('food_id')->constrained('food')->onDelete('cascade');

            // الكمية
            $table->decimal('quantity', 8, 2)->default(1);    // الكمية
            $table->text('unit')->nullable();                  // الوحدة (جرام، كوب، ملعقة)
            $table->decimal('grams', 8, 2)->nullable();       // الوزن بالجرام

            // القيم الغذائية المحسوبة لهذه الكمية
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->default(0);
            $table->decimal('fats', 8, 2)->default(0);

            $table->text('notes')->nullable();                // ملاحظات إضافية

            $table->timestamps();

            $table->index(['user_meal_id', 'food_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_ingredients');
    }
};
