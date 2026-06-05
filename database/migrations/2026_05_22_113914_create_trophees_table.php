<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trophees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('year_of');
            $table->unsignedTinyInteger('rank');
            $table->text('description')->nullable();
            $table->timestamps();

            // Un seul vainqueur par rang (1/2/3) et par année.
            $table->unique(['year_of', 'rank']);
            $table->index('year_of');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trophees');
    }
};
