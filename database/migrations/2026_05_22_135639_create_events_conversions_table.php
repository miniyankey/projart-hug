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
        Schema::create('events_conversions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index();
            $table->boolean('appointment_click')->default(false);
            $table->string('source')->nullable();
            $table->foreignId('collect_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_conversions');
    }
};
