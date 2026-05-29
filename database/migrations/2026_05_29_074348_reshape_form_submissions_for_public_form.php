<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reshape the form_submissions table to match the public form:
     * a single contact name, no postal address, a trophy participation flag
     * and one-or-many preferred dates for collect requests.
     */
    public function up(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'company_address',
                'company_city',
                'company_postal_code',
                'lastname',
                'preferred_date',
            ]);

            $table->renameColumn('firstname', 'name');
            $table->boolean('trophy_participation')->default(false)->after('message');
            $table->json('preferred_dates')->nullable()->after('trophy_participation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_submissions', function (Blueprint $table) {
            $table->dropColumn(['trophy_participation', 'preferred_dates']);

            $table->renameColumn('name', 'firstname');
            $table->string('lastname')->after('firstname');
            $table->string('company_address')->after('company_name');
            $table->string('company_city')->after('company_address');
            $table->string('company_postal_code')->after('company_city');
            $table->date('preferred_date')->nullable();
        });
    }
};
