<?php
// database/migrations/2024_01_01_000010_create_subscriptions_table.php
// جدول الاشتراكات - إدارة الدفع والاشتراكات

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // نوع الاشتراك - TEXT للترجمة
            $table->text('plan_name');                        // اسم الخطة

            // نوع الخطة
            $table->enum('plan_type', ['free', 'monthly', 'yearly', 'lifetime'])->default('free');

            // السعر
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('SAR');    // العملة

            // فترة الاشتراك
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('trial_ends_at')->nullable();   // نهاية الفترة التجريبية

            // حالة الاشتراك
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending', 'trial'])->default('pending');
            $table->boolean('auto_renew')->default(true);     // التجديد التلقائي

            // بيانات الدفع
            $table->string('payment_method')->nullable();     // طريقة الدفع
            $table->string('transaction_id')->nullable();     // معرف المعاملة
            $table->string('receipt_url')->nullable();        // رابط الإيصال

            // بيانات المتجر (App Store / Google Play)
            $table->string('store_product_id')->nullable();   // معرف المنتج في المتجر
            $table->string('store_transaction_id')->nullable();
            $table->json('store_receipt')->nullable();        // إيصال المتجر

            $table->timestamps();

            // فهارس
            $table->index(['user_id', 'status']);
            $table->index('ends_at');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
