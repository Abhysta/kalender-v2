<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->dropColumn('override_end_date');
        });
    }

    public function down(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->date('override_end_date')->nullable()->after('override_start_date');
        });
    }
};
