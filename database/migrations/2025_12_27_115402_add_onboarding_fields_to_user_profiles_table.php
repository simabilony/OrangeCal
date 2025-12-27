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
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->decimal('weekly_target', 5, 2)->nullable()->after('target_weight');
            $table->string('tdee_goal')->nullable()->after('activity_level');
            $table->boolean('tried_another_app')->nullable()->after('health_conditions');
            $table->string('hearing_about_us')->nullable()->after('tried_another_app');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['weekly_target', 'tdee_goal', 'tried_another_app', 'hearing_about_us']);
        });
    }
};
