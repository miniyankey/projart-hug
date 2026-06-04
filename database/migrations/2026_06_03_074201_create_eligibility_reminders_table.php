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
        Schema::create('eligibility_reminders', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('locale', 5)->default('fr');
            $table->foreignId('collect_id')->nullable()->constrained()->nullOnDelete();
            $table->date('eligible_at');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['sent_at', 'eligible_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eligibility_reminders');
    }
};
