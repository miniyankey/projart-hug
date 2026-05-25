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
        Schema::create('events_eligibilite_steps', function (Blueprint $table) {
            $table->id();
            $table->string('session_id');
            $table->foreignId('collect_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step');
            $table->string('result')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index('session_id');
            $table->index(['collect_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_eligibilite_steps');
    }
};
