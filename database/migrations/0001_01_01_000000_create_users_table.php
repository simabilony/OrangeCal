<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // المفتاح الأساسي
            $table->id();

            // بيانات المصادقة - طرق تسجيل الدخول المتعددة
            $table->string('google_id')->nullable()->unique(); // معرف جوجل
            $table->string('apple_id')->nullable()->unique();  // معرف أبل
            $table->string('mobile_id')->nullable()->unique(); // معرف الجوال

            // البيانات الأساسية - TEXT للحقول القابلة للترجمة
            $table->text('name')->nullable();           // الاسم (عربي/إنجليزي)
            $table->string('email')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();     // كلمة المرور (اختياري للتسجيل الاجتماعي)

            // بيانات الجهاز والإشعارات
            $table->string('fcm_token')->nullable();    // رمز Firebase للإشعارات
            $table->string('device_type')->nullable();  // نوع الجهاز (ios/android)
            $table->string('device_id')->nullable();    // معرف الجهاز

            // حالة الحساب
            $table->boolean('is_active')->default(true);      // هل الحساب نشط
            $table->boolean('is_onboarded')->default(false);  // هل أكمل التسجيل
            $table->boolean('is_premium')->default(false);    // هل مشترك مدفوع

            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // الحذف الناعم

            // الفهارس للبحث السريع
            $table->index('is_active');
            $table->index('is_premium');
            $table->index('created_at');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
