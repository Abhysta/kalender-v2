<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_calendar_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('annual_calendar_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_template_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['annual_calendar_id', 'planned_start_date'], 'aca_calendar_planned_start_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_calendar_activities');
    }
};
