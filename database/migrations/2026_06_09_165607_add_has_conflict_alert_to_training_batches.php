<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->boolean('has_conflict_alert')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('training_batches', function (Blueprint $table) {
            $table->dropColumn('has_conflict_alert');
        });
    }
};
