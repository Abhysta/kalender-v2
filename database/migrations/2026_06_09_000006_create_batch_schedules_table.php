<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('batch_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_phase_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('sequence')->default(0);
            $table->string('activity_type')->nullable();
            $table->string('conflict_group')->nullable();
            $table->string('color', 20)->default('#0369A1');
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_manual')->default(false);
            $table->boolean('is_alert')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['training_batch_id', 'start_date', 'end_date']);
            $table->index('conflict_group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_schedules');
    }
};
