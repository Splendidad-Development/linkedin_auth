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
        Schema::table('linkedin_accounts', function (Blueprint $table) {
            $table->string('last_post_hash')->nullable()->after('profile_picture');
            $table->timestamp('last_posted_at')->nullable()->after('last_post_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('linkedin_accounts', function (Blueprint $table) {
            $table->dropColumn(['last_post_hash', 'last_posted_at']);
        });
    }
};
