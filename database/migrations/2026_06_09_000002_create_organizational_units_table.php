<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizational_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('organizational_units')->insert([
            ['code' => 'TEKPIM',    'name' => 'Tekpim',      'description' => 'Unit Teknologi dan Pimpinan',   'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FUNGSIONAL','name' => 'Fungsional',  'description' => 'Unit Fungsional',               'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SEKRETARIAT','name' => 'Sekretariat','description' => 'Unit Sekretariat',              'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PENKOM',    'name' => 'Penkom',      'description' => 'Unit Pengembangan Kompetensi',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('organizational_units');
    }
};
