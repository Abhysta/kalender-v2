<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conflict_rules', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. 'seminar_overlap', 'wi_overlap'
            $table->string('name');
            $table->string('rule_type'); // date_overlap, shared_resource, ...
            $table->string('scope')->nullable(); // e.g. 'global', 'unit'
            $table->string('severity')->default('blocking');
            $table->json('config')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('conflict_group_id')->nullable()
                ->constrained('conflict_groups')->nullOnDelete();
            $table->timestamps();
        });

        // Seed the two conflict rules the existing ConflictCheckerService already implements,
        // so they're visible/toggleable in the admin UI without inventing a generic rule engine.
        \DB::table('conflict_rules')->insert([
            [
                'code' => 'seminar_overlap', 'name' => 'Konflik Seminar', 'rule_type' => 'date_overlap',
                'scope' => 'global', 'severity' => 'blocking', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'code' => 'wi_overlap', 'name' => 'Konflik Widyaiswara', 'rule_type' => 'shared_resource',
                'scope' => 'global', 'severity' => 'blocking', 'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('conflict_rules');
    }
};
