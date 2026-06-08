<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Le statut d'une collecte est désormais entièrement dérivé de sa date
     * (« en cours » jusqu'à une semaine après le jour, « terminée » ensuite).
     * Le flag manuel `is_active` devient redondant et trompeur : on le supprime.
     */
    public function up(): void
    {
        Schema::table('collects', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('collects', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
        });
    }
};
