<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            // Drop the simple unique on date; replace with composite
            $table->dropUnique(['date']);
            $table->foreignId('organizational_unit_id')
                ->nullable()
                ->after('is_national')
                ->constrained()
                ->nullOnDelete();
            // Unique per date per unit (null = global; enforced per-unit at app level)
            $table->unique(['date', 'organizational_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::table('holidays', function (Blueprint $table) {
            $table->dropUnique(['date', 'organizational_unit_id']);
            $table->dropForeign(['organizational_unit_id']);
            $table->dropColumn('organizational_unit_id');
            $table->unique('date');
        });
    }
};
