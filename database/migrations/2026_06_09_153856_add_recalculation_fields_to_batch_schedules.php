<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->boolean('is_anchor')->default(false)->after('is_manual');
            $table->string('recalculation_source', 20)->default('generated')->after('is_anchor');
            $table->timestamp('recalculated_at')->nullable()->after('recalculation_source');
        });
    }

    public function down(): void
    {
        Schema::table('batch_schedules', function (Blueprint $table) {
            $table->dropColumn(['is_anchor', 'recalculation_source', 'recalculated_at']);
        });
    }
};
