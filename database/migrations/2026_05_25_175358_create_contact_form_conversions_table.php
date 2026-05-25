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
        Schema::create('contact_form_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->boolean('form_click')->default(false);
            $table->boolean('form_sent')->default(false);
            $table->boolean('trophee_participation')->default(false);
            $table->timestamps();

            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_form_conversions');
    }
};
