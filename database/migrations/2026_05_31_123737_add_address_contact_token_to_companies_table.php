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
        Schema::table('companies', function (Blueprint $table) {
            // Token permanent par entreprise : alimente le lien co-brandé stable
            // /{slug}/{token}/collecte (la page reste 404 sans collecte active).
            $table->string('token')->unique()->after('slug');

            $table->string('contact_name')->nullable()->after('email_contact');
            $table->string('contact_phone')->nullable()->after('contact_name');
            $table->string('color_secondary')->nullable()->after('color');

            // Adresse du siège de l'entreprise (distincte du lieu de collecte = Place).
            $table->string('street')->nullable()->after('contact_phone');
            $table->string('postal_code')->nullable()->after('street');
            $table->string('city')->nullable()->after('postal_code');
            $table->string('country')->default('CH')->after('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['token']);
            $table->dropColumn([
                'token',
                'contact_name',
                'contact_phone',
                'color_secondary',
                'street',
                'postal_code',
                'city',
                'country',
            ]);
        });
    }
};
