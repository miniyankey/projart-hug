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
        Schema::table('contact_form_conversions', function (Blueprint $table) {
            // Onglet actif du widget (contact / collect_request) au moment de
            // la dernière action trackée. Attribution « best effort » : le
            // dernier onglet touché gagne (l'utilisateur peut basculer).
            $table->string('type')->nullable()->after('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_form_conversions', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
