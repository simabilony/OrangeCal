<?php
// database/migrations/2024_01_01_000003_create_meal_schedules_table.php
// جدول مواعيد الوجبات - تفضيلات توقيت الوجبات

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meal_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // نوع الوجبة - TEXT للترجمة
            $table->text('meal_type');              // فطور، غداء، عشاء، وجبة خفيفة

            // توقيت الوجبة
            $table->time('scheduled_time');         // الوقت المحدد
            $table->boolean('reminder_enabled')->default(true); // تفعيل التذكير
            $table->integer('reminder_minutes_before')->default(15); // دقائق قبل التذكير

            // السعرات المخصصة لهذه الوجبة
            $table->integer('target_calories')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

        
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_schedules');
    }
};
