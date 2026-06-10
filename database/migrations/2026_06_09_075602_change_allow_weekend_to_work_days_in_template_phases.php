<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->tinyInteger('work_days')->default(5)->after('day_type');
        });

        // Migrate existing data: allow_weekend=true → 6, false → 5
        DB::table('template_phases')->where('allow_weekend', true)->update(['work_days' => 6]);

        Schema::table('template_phases', function (Blueprint $table) {
            $table->dropColumn('allow_weekend');
        });
    }

    public function down(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->boolean('allow_weekend')->default(false)->after('day_type');
        });

        DB::table('template_phases')->where('work_days', '>=', 6)->update(['allow_weekend' => true]);

        Schema::table('template_phases', function (Blueprint $table) {
            $table->dropColumn('work_days');
        });
    }
};
