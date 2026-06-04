<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rend collect_id nullable : le jeu d'éligibilité public (hors co-branding)
     * n'est rattaché à aucune collecte, ses sessions sont trackées avec
     * collect_id = null (funnel global, exclu des vues KPI par entreprise).
     */
    public function up(): void
    {
        Schema::table('events_eligibilite_steps', function (Blueprint $table) {
            $table->dropForeign(['collect_id']);
            $table->foreignId('collect_id')->nullable()->change();
            $table->foreign('collect_id')->references('id')->on('collects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse : repasse en NOT NULL (échoue s'il existe des lignes collect_id null).
     */
    public function down(): void
    {
        Schema::table('events_eligibilite_steps', function (Blueprint $table) {
            $table->dropForeign(['collect_id']);
            $table->foreignId('collect_id')->nullable(false)->change();
            $table->foreign('collect_id')->references('id')->on('collects')->cascadeOnDelete();
        });
    }
};
