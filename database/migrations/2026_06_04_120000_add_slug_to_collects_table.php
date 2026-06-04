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
        Schema::table('collects', function (Blueprint $table) {
            // Slug par collecte (date + ville + token) qui sert de segment d'URL co-brandée :
            // /{slug-entreprise}/{slug-collecte}/collecte. Unique car il embarque le token.
            $table->string('slug')->unique()->after('token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('collects', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
