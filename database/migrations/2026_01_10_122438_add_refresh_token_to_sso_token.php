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
        Schema::table('sso_token', function (Blueprint $table) {
            $table->text('refresh_token')->nullable()->after('original_token');
            $table->timestamp('refresh_expires_at')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sso_token', function (Blueprint $table) {
            $table->dropColumn('refresh_token');
            $table->dropColumn('refresh_expires_at');
        });
    }
};
