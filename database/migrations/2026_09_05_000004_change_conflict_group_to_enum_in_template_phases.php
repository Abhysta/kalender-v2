<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Existing rows store '' rather than NULL for "no conflict group" - the
        // enum only accepts NULL or 'seminar', so normalize before converting.
        DB::table('template_phases')
            ->whereNotIn('conflict_group', ['seminar'])
            ->update(['conflict_group' => null]);

        Schema::table('template_phases', function (Blueprint $table) {
            $table->enum('conflict_group', ['seminar'])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('template_phases', function (Blueprint $table) {
            $table->string('conflict_group')->nullable()->change();
        });
    }
};
