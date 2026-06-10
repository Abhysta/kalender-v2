<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wi_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_schedule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('widyaiswara_id')->constrained()->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['widyaiswara_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wi_assignments');
    }
};
