<?php
// database/migrations/2024_01_01_000005_create_user_meals_table.php
// جدول وجبات المستخدم - الوجبات المخصصة التي ينشئها المستخدم

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('name');
            $table->text('description')->nullable();

            // القيم الغذائية الإجمالية (محسوبة من المكونات)
            $table->decimal('total_calories', 8, 2)->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fats', 8, 2)->default(0);

            $table->text('meal_type')->nullable();
            $table->integer('servings')->default(1);
            $table->integer('prep_time')->nullable();
            $table->text('instructions')->nullable();

            $table->string('image_url')->nullable();
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_public')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'is_favorite']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meals');
    }
};
