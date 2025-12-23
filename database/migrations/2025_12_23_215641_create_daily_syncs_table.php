<?php
// database/migrations/2024_01_01_000011_create_daily_syncs_table.php
// جدول المزامنة اليومية - ملخص البيانات اليومية

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_syncs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // التاريخ
            $table->date('sync_date');                        // تاريخ المزامنة

            // ملخص السعرات
            $table->decimal('total_calories_consumed', 10, 2)->default(0); // السعرات المستهلكة
            $table->decimal('total_calories_burned', 10, 2)->default(0);   // السعرات المحروقة
            $table->decimal('net_calories', 10, 2)->default(0);            // صافي السعرات

            // ملخص الماكروز
            $table->decimal('total_protein', 8, 2)->default(0);
            $table->decimal('total_carbs', 8, 2)->default(0);
            $table->decimal('total_fats', 8, 2)->default(0);
            $table->decimal('total_fiber', 8, 2)->default(0);
            $table->decimal('total_sugar', 8, 2)->default(0);

            // الماء
            $table->integer('water_intake')->default(0);      // كمية الماء (مل)

            // التمارين
            $table->integer('total_exercise_minutes')->default(0);
            $table->integer('total_steps')->default(0);

            // الأهداف
            $table->decimal('calorie_goal', 10, 2)->default(0);   // هدف السعرات
            $table->decimal('goal_progress', 5, 2)->default(0);   // نسبة تحقيق الهدف

            // الوزن (إذا تم تسجيله)
            $table->decimal('weight_logged', 5, 2)->nullable();

            // حالة المزامنة
            $table->boolean('is_complete')->default(false);   // هل اكتمل اليوم
            $table->timestamp('last_synced_at')->nullable();  // آخر مزامنة

            $table->timestamps();

            // فهرس فريد لمنع التكرار
            $table->unique(['user_id', 'sync_date']);
            $table->index('sync_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_syncs');
    }
};
