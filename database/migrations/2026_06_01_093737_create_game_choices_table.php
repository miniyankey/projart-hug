<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('view_id')->nullable()->constrained()->nullOnDelete();
            $table->string('text');
            $table->text('descr')->nullable();
            /* Plus ineligibility est grand plus il devient prioritaire sur les autres, sauf s'il est négatif et là il prime sur tout */
            $table->integer('ineligibility'); // <0 = à vie, 0 = éligible, >0 = inéligible en jours
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->index(['question_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('choices');
    }
};
