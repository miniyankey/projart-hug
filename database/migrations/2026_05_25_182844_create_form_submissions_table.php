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
        Schema::create('form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('company_name');
            $table->string('company_address');
            $table->string('company_city');
            $table->string('company_postal_code');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('contact_email');
            $table->string('message')->nullable();
            $table->date('preferred_date')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('contact_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_submissions');
    }
};
