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

            // الأسماء - TEXT للترجمة والبحث
            $table->text('name');                    // الاسم الافتراضي
            // الوصف والتصنيف
            $table->text('description')->nullable();  // الوصف
            $table->text('category')->nullable();     // التصنيف (فواكه، خضروات، لحوم)

            // الباركود والمصدر
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

            // حجم الحصة
            $table->decimal('serving_size', 8, 2)->default(100); // حجم الحصة
            $table->text('serving_unit')->nullable();  // وحدة القياس (جرام، كوب، قطعة)

            // معلومات إضافية
            $table->boolean('is_halal')->default(true);       // حلال
            $table->boolean('is_vegetarian')->default(false); // نباتي
            $table->boolean('is_vegan')->default(false);      // نباتي صرف
            $table->boolean('is_gluten_free')->default(false); // خالي من الجلوتين
            $table->boolean('is_verified')->default(false);   // تم التحقق

            // الصورة
            $table->string('image_url')->nullable();

            $table->timestamps();

            // فهارس للبحث السريع
            $table->index('barcode');
            $table->index('is_verified');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
