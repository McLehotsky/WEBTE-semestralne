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
        Schema::table('edit_history', function (Blueprint $table) {
            $table->timestamp('used_at')->after('accessed_via')->default(now());
        });
    }

    public function down(): void
    {
        Schema::table('edit_history', function (Blueprint $table) {
            $table->dropColumn('used_at');
        });
    }

};
