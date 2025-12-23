<?php
// database/migrations/2024_01_01_000007_create_food_logs_table.php
// جدول سجل الطعام اليومي - تتبع ما يأكله المستخدم

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('food_logs', function (Blueprint $table) {
            $table->id();

            // الربط بالمستخدم
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // الربط بالطعام أو الوجبة (أحدهما)
            $table->foreignId('food_id')->nullable()->constrained('food')->onDelete('set null');
            $table->foreignId('user_meal_id')->nullable()->constrained('user_meals')->onDelete('set null');

            // اسم الطعام - TEXT للترجمة (للأطعمة الممسوحة بالذكاء الاصطناعي)
            $table->text('name')->nullable();                 // اسم الطعام


            // التاريخ والوقت
            $table->date('log_date');                         // تاريخ السجل
            $table->time('log_time')->nullable();             // وقت الأكل
            $table->text('meal_type')->nullable();            // نوع الوجبة (فطور، غداء، إلخ)

            // الكمية
            $table->decimal('quantity', 8, 2)->default(1);
            $table->text('unit')->nullable();
            $table->decimal('grams', 8, 2)->nullable();

            // القيم الغذائية
            $table->decimal('calories', 8, 2)->default(0);
            $table->decimal('protein', 8, 2)->default(0);
            $table->decimal('carbs', 8, 2)->default(0);
            $table->decimal('fats', 8, 2)->default(0);
            $table->decimal('fiber', 8, 2)->default(0);
            $table->decimal('sugar', 8, 2)->default(0);

            // مصدر الإدخال
            $table->enum('source', ['manual', 'barcode', 'ai_scan', 'meal', 'search'])->default('manual');
            $table->string('barcode_scanned')->nullable();    // الباركود الممسوح
            $table->string('image_url')->nullable();          // صورة الطعام

            // بيانات الذكاء الاصطناعي
            $table->json('ai_response')->nullable();          // استجابة الذكاء الاصطناعي
            $table->decimal('ai_confidence', 5, 2)->nullable(); // نسبة الثقة

            // معلومات إضافية
            $table->boolean('is_halal')->default(true);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // فهارس للبحث والتقارير
            $table->index(['user_id', 'log_date']);
            $table->index('log_date');
            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('food_logs');
    }
};
