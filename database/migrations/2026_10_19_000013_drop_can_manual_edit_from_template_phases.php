<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * can_manual_edit was never read anywhere — manual-edit permission on a generated
     * schedule is actually gated by batch_schedules.is_locked, unrelated to this column.
     */
    public function up(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->dropColumn('can_manual_edit');
        });
    }

    public function down(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->boolean('can_manual_edit')->default(false)->after('alert_type');
        });
    }
};
