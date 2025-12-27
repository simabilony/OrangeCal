<?php
// database/migrations/2024_01_01_000002_create_user_profiles_table.php
// جدول الملفات الشخصية - بيانات الصحة واللياقة

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('age')->nullable();

            $table->decimal('height', 5, 2)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->decimal('target_weight', 5, 2)->nullable();
            $table->decimal('bmi', 4, 2)->nullable();         // مؤشر كتلة الجسم

            $table->text('goal')->nullable();                 // الهدف (إنقاص/زيادة/ثبات)
            $table->text('activity_level')->nullable();

            // السعرات والماكروز اليومية
            $table->integer('daily_calories')->nullable();    // السعرات اليومية المستهدفة
            $table->integer('daily_protein')->nullable();     // البروتين اليومي (جرام)
            $table->integer('daily_carbs')->nullable();       // الكربوهيدرات اليومية (جرام)
            $table->integer('daily_fats')->nullable();        // الدهون اليومية (جرام)
            $table->integer('daily_water')->nullable();       // الماء اليومي (مل)

            // التفضيلات الغذائية - TEXT للترجمة
            $table->text('dietary_preferences')->nullable();  // التفضيلات (نباتي، حلال، إلخ)
            $table->text('allergies')->nullable();            // الحساسيات الغذائية
            $table->text('health_conditions')->nullable();    // الحالات الصحية

            $table->string('preferred_language', 5)->default('ar'); // اللغة المفضلة
            $table->string('timezone')->default('Asia/Riyadh');
            $table->boolean('notifications_enabled')->default(true);

            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
