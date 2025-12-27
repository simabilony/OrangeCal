<?php
// database/migrations/2024_01_01_000008_create_saved_foods_table.php
// جدول الأطعمة المحفوظة - المفضلة لدى المستخدم

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_food', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_id')->constrained('food')->onDelete('cascade');

            // ملاحظات المستخدم
            $table->text('notes')->nullable();                // ملاحظات شخصية
            $table->text('custom_name')->nullable();          // اسم مخصص

            // الكمية الافتراضية
            $table->decimal('default_quantity', 8, 2)->default(1);
            $table->text('default_unit')->nullable();

            $table->timestamps();

            $table->unique(['user_id', 'food_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_food');
    }
};
