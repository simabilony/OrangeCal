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

            // اسم الوجبة - TEXT للترجمة
            $table->text('name');                     // اسم الوجبة
            $table->text('description')->nullable();  // وصف الوجبة

            // القيم الغذائية الإجمالية (محسوبة من المكونات)
            $table->decimal('total_calories', 8, 2)->default(0);
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fats', 8, 2)->default(0);

            // معلومات إضافية
            $table->text('meal_type')->nullable();    // نوع الوجبة
            $table->integer('servings')->default(1); // عدد الحصص
            $table->integer('prep_time')->nullable(); // وقت التحضير بالدقائق
            $table->text('instructions')->nullable(); // طريقة التحضير

            // الصورة والحالة
            $table->string('image_url')->nullable();
            $table->boolean('is_favorite')->default(false); // مفضلة
            $table->boolean('is_public')->default(false);   // عامة للمشاركة

            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index(['user_id', 'is_favorite']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_meals');
    }
};
