<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conflict_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conflict_rule_id')->nullable()->constrained('conflict_rules')->nullOnDelete();
            $table->string('rule_type');
            $table->foreignId('batch_schedule_id')->constrained('batch_schedules')->cascadeOnDelete();
            $table->foreignId('related_batch_schedule_id')->nullable()->constrained('batch_schedules')->cascadeOnDelete();
            $table->foreignId('conflict_group_id')->nullable()->constrained('conflict_groups')->nullOnDelete();
            $table->enum('status', ['open', 'resolved', 'ignored'])->default('open');
            $table->text('details')->nullable();
            // Deterministic dedup key (rule_type + sorted schedule ids) — repeated
            // evaluations never create duplicate rows for the same conflict.
            $table->string('dedup_key')->unique();
            $table->timestamp('detected_at');
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conflict_alerts');
    }
};
