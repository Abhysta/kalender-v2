<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Detection is no longer hardcoded to the literal 'seminar' conflict_group — it now
     * applies to any conflict group. Rename the existing seeded rule to reflect that,
     * preserving its id so existing conflict_alerts.conflict_rule_id references stay valid.
     */
    public function up(): void
    {
        DB::table('conflict_rules')
            ->where('code', 'seminar_overlap')
            ->update(['code' => 'group_overlap', 'name' => 'Konflik Grup (Overlap)']);

        DB::table('conflict_alerts')
            ->where('rule_type', 'seminar_overlap')
            ->update(['rule_type' => 'group_overlap']);
    }

    public function down(): void
    {
        DB::table('conflict_rules')
            ->where('code', 'group_overlap')
            ->update(['code' => 'seminar_overlap', 'name' => 'Konflik Seminar']);

        DB::table('conflict_alerts')
            ->where('rule_type', 'group_overlap')
            ->update(['rule_type' => 'seminar_overlap']);
    }
};
