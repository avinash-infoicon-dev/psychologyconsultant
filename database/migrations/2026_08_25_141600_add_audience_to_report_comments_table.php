<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_comments', function (Blueprint $table) {
            $table->string('audience', 20)->default('reporter')->after('is_internal');
            $table->index(['report_id', 'audience']);
        });

        DB::table('report_comments')->where('is_internal', true)->update(['audience' => 'internal']);
        DB::table('report_comments')->where('is_internal', false)->update(['audience' => 'reporter']);
    }

    public function down(): void
    {
        Schema::table('report_comments', function (Blueprint $table) {
            $table->dropIndex(['report_id', 'audience']);
            $table->dropColumn('audience');
        });
    }
};
