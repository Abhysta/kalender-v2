<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('annual_calendars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organizational_unit_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('year');
            $table->string('name');
            $table->timestamp('archived_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['year', 'organizational_unit_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('annual_calendars');
    }
};
