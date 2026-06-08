<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Capture the city of the requester so admins can spot Geneva-based
     * companies (already meeting the local-collection condition).
     */
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->string('city')->nullable()->after('company_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn('city');
        });
    }
};
