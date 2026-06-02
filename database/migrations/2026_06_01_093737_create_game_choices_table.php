<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('game_questions')->cascadeOnDelete();
            $table->foreignId('view_id')->nullable()->constrained('game_views')->nullOnDelete();
            $table->string('text');
            $table->text('descr')->nullable();
            $table->boolean('eligible'); // true = éligible, false = inéligible
            $table->integer('ineligibility_days')->nullable(); // null = éligible, <0 = à vie, >0 = inéligible en jours
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->index(['question_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_choices');
    }
};
