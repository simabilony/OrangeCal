<?php
// database/migrations/2024_01_01_000004_create_foods_table.php
// جدول الأطعمة الرئيسي - قاعدة بيانات الأطعمة

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food', function (Blueprint $table) {
            $table->id();

            $table->text('name');
            $table->text('description')->nullable();
            $table->text('category')->nullable();

            $table->string('barcode')->nullable()->unique(); // رمز الباركود
            $table->string('source')->nullable();     // مصدر البيانات (manual, api, scan)

            // القيم الغذائية لكل 100 جرام
            $table->decimal('calories', 8, 2)->default(0);    // السعرات
            $table->decimal('protein', 8, 2)->default(0);     // البروتين
            $table->decimal('carbs', 8, 2)->default(0);       // الكربوهيدرات
            $table->decimal('fats', 8, 2)->default(0);        // الدهون
            $table->decimal('fiber', 8, 2)->default(0);       // الألياف
            $table->decimal('sugar', 8, 2)->default(0);       // السكر
            $table->decimal('sodium', 8, 2)->default(0);      // الصوديوم (ملجم)
            $table->decimal('saturated_fat', 8, 2)->default(0); // الدهون المشبعة
            $table->decimal('cholesterol', 8, 2)->default(0); // الكوليسترول

            $table->decimal('serving_size', 8, 2)->default(100); // حجم الحصة
            $table->text('serving_unit')->nullable();  // وحدة القياس (جرام، كوب، قطعة)

            $table->boolean('is_halal')->default(true);
            $table->boolean('is_vegetarian')->default(false); // نباتي
            $table->boolean('is_vegan')->default(false);      // نباتي صرف
            $table->boolean('is_gluten_free')->default(false); // خالي من الجلوتين
            $table->boolean('is_verified')->default(false);   // تم التحقق

            $table->string('image_url')->nullable();

            $table->timestamps();

            $table->index('barcode');
            $table->index('is_verified');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
