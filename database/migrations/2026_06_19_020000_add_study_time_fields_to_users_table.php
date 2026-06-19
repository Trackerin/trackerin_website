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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->unsignedBigInteger('total_study_time')->default(0)->after('last_login_at');
            $table->integer('current_streak')->default(0)->after('total_study_time');
            $table->date('last_active_date')->nullable()->after('current_streak');
            $table->json('weekly_activity')->nullable()->after('last_active_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_login_at',
                'total_study_time',
                'current_streak',
                'last_active_date',
                'weekly_activity'
            ]);
        });
    }
};
