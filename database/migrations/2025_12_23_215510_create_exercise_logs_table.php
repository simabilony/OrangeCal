<?php
// database/migrations/2024_01_01_000009_create_exercise_logs_table.php
// جدول سجل التمارين - تتبع النشاط البدني

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->text('type');                             // نوع التمرين
            $table->text('description')->nullable();          // وصف التمرين

            // التاريخ والوقت
            $table->date('log_date');                         // تاريخ التمرين
            $table->time('start_time')->nullable();           // وقت البداية
            $table->time('end_time')->nullable();             // وقت النهاية

            // مدة وشدة التمرين
            $table->integer('duration')->default(0);          // المدة بالدقائق
            $table->enum('intensity', ['low', 'mid', 'high'])->default('mid'); // الشدة

            // السعرات المحروقة
            $table->decimal('calories_burned', 8, 2)->default(0);

            // بيانات إضافية
            $table->decimal('distance', 8, 2)->nullable();    // المسافة (كم)
            $table->integer('steps')->nullable();             // عدد الخطوات
            $table->integer('heart_rate_avg')->nullable();    // متوسط نبضات القلب

            // مصدر البيانات
            $table->enum('source', ['manual', 'ai', 'device', 'apple_health', 'google_fit'])->default('manual');
            $table->json('ai_response')->nullable();          // استجابة الذكاء الاصطناعي

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // فهارس
            $table->index(['user_id', 'log_date']);
            $table->index('log_date');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_logs');
    }
};
