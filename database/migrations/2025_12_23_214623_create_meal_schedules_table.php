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

            $table->text('meal_type');              // فطور، غداء، عشاء، وجبة خفيفة

            $table->time('scheduled_time');
            $table->boolean('reminder_enabled')->default(true);
            $table->integer('reminder_minutes_before')->default(15);

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
