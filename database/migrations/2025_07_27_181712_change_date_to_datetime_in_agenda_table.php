<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Change the 'start' and 'end' columns to be DATETIME
            $table->dateTime('start')->change();
            $table->dateTime('end')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agenda', function (Blueprint $table) {
            // Revert back to DATE if needed
            $table->date('start')->change();
            $table->date('end')->nullable()->change();
        });
    }
};