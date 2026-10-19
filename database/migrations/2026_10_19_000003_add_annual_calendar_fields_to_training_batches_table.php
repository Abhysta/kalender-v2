<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->foreignId('annual_calendar_activity_id')->nullable()
                ->after('organizational_unit_id')
                ->constrained('annual_calendar_activities')->nullOnDelete();
            $table->date('override_start_date')->nullable()->after('end_date');
            $table->date('override_end_date')->nullable()->after('override_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('annual_calendar_activity_id');
            $table->dropColumn(['override_start_date', 'override_end_date']);
        });
    }
};
