<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Dédoublonnage : par e-mail, on conserve le rappel à la date
        // d'éligibilité la plus lointaine (durée la plus longue) ; à égalité,
        // le plus récent (id le plus grand). Les autres (durée plus courte)
        // sont supprimés.
        DB::table('eligibility_reminders')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email')
            ->each(function (string $email): void {
                $keepId = DB::table('eligibility_reminders')
                    ->where('email', $email)
                    ->orderByDesc('eligible_at')
                    ->orderByDesc('id')
                    ->value('id');

                DB::table('eligibility_reminders')
                    ->where('email', $email)
                    ->where('id', '!=', $keepId)
                    ->delete();
            });

        // 2) Un seul rappel par adresse e-mail.
        Schema::table('eligibility_reminders', function (Blueprint $table) {
            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eligibility_reminders', function (Blueprint $table) {
            $table->dropUnique(['email']);
        });
    }
};
