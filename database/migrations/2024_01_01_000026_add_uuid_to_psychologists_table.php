<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Legacy psychologists migration disabled. The professionals table
         * creates and manages its own UUID column.
         *
         * Schema::table('psychologists', function (Blueprint $table) {
         *     $table->uuid('uuid')->nullable()->unique()->after('id');
         * });
         */
    }

    public function down(): void
    {
        // Schema::table('psychologists', fn (Blueprint $table) => $table->dropColumn('uuid'));
    }
};
