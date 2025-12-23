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

            // الربط بالمستخدم
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // البيانات الشخصية
            $table->enum('gender', ['male', 'female'])->nullable(); // الجنس
            $table->date('birth_date')->nullable();                  // تاريخ الميلاد
            $table->integer('age')->nullable();                      // العمر

            // القياسات الجسدية
            $table->decimal('height', 5, 2)->nullable();      // الطول بالسنتيمتر
            $table->decimal('weight', 5, 2)->nullable();      // الوزن بالكيلوجرام
            $table->decimal('target_weight', 5, 2)->nullable(); // الوزن المستهدف
            $table->decimal('bmi', 4, 2)->nullable();         // مؤشر كتلة الجسم

            // الأهداف والنشاط - TEXT للترجمة
            $table->text('goal')->nullable();                 // الهدف (إنقاص/زيادة/ثبات)
            $table->text('activity_level')->nullable();       // مستوى النشاط

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

            // إعدادات إضافية
            $table->string('preferred_language', 5)->default('ar'); // اللغة المفضلة
            $table->string('timezone')->default('Asia/Riyadh');     // المنطقة الزمنية
            $table->boolean('notifications_enabled')->default(true); // تفعيل الإشعارات

            $table->timestamps();

            // فهرس فريد لضمان ملف شخصي واحد لكل مستخدم
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
