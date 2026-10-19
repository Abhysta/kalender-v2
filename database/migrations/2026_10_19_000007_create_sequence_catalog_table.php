<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sequence_catalog', function (Blueprint $table) {
            $table->id();
            // Unique canonical identity for an activity/phase name across all Excel imports.
            $table->string('normalized_name')->unique();
            $table->string('display_name'); // first-seen original name, preserved for audit
            $table->foreignId('default_conflict_group_id')->nullable()
                ->constrained('conflict_groups')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sequence_catalog');
    }
};
