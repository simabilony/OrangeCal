<?php
// database/migrations/2024_01_01_000012_create_notifications_table.php
// جدول الإشعارات - إشعارات التطبيق

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();                    // معرف UUID

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // نوع الإشعار
            $table->string('type');                           // نوع الإشعار (class name)

            // المحتوى - TEXT للترجمة
            $table->text('title');                            // العنوان
            $table->text('body');                             // المحتوى
            // البيانات الإضافية
            $table->json('data')->nullable();                 // بيانات إضافية

            // الإجراء
            $table->string('action_type')->nullable();        // نوع الإجراء (open_screen, open_url)
            $table->string('action_value')->nullable();       // قيمة الإجراء

            // الحالة
            $table->timestamp('read_at')->nullable();         // وقت القراءة
            $table->timestamp('sent_at')->nullable();         // وقت الإرسال
            $table->boolean('is_push_sent')->default(false);  // هل تم إرسال Push

            $table->timestamps();

            // فهارس
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
