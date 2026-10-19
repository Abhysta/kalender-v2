<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->foreignId('sequence_catalog_id')->nullable()->after('name')
                ->constrained('sequence_catalog')->nullOnDelete();
            $table->foreignId('conflict_group_id')->nullable()->after('conflict_group')
                ->constrained('conflict_groups')->nullOnDelete();
        });

        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->foreignId('conflict_group_id')->nullable()->after('conflict_group')
                ->constrained('conflict_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sequence_catalog_id');
            $table->dropConstrainedForeignId('conflict_group_id');
        });

        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->dropConstrainedForeignId('conflict_group_id');
        });
    }
};
