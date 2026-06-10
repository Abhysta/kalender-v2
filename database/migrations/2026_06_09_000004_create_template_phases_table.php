<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('template_phases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_template_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('sequence')->default(0);
            $table->integer('duration')->default(1);
            $table->enum('duration_unit', ['day', 'hour'])->default('day');
            $table->integer('offset_days')->default(0);
            $table->enum('day_type', ['working_day', 'calendar_day'])->default('working_day');
            $table->boolean('allow_weekend')->default(false);
            $table->string('activity_type')->nullable();
            $table->string('conflict_group')->nullable();
            $table->boolean('is_alert')->default(false);
            $table->string('alert_type')->nullable();
            $table->boolean('can_manual_edit')->default(false);
            $table->string('color', 20)->default('#0369A1');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('template_phases');
    }
};
