<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_template_id')->constrained()->restrictOnDelete();
            $table->foreignId('organizational_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('batch_number');
            $table->integer('year');
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->integer('participant_count')->default(0);
            $table->enum('status', ['draft', 'generated', 'finished'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_batches');
    }
};
