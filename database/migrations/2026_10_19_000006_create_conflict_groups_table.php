<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conflict_groups', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Backfill: every distinct non-empty conflict_group string already in use
        // becomes a stable canonical group, so existing data keeps working unchanged.
        $existingCodes = \DB::table('template_phases')
            ->whereNotNull('conflict_group')
            ->where('conflict_group', '!=', '')
            ->distinct()
            ->pluck('conflict_group');

        foreach ($existingCodes as $code) {
            \DB::table('conflict_groups')->insertOrIgnore([
                'code'       => $code,
                'name'       => $code,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('conflict_groups');
    }
};
