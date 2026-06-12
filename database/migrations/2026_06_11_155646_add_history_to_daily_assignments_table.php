<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('daily_assignments', function (Blueprint $table) {
            $table->json('created_tasks')->nullable();
            $table->json('created_subtasks')->nullable();
            $table->json('updated_tasks')->nullable();
            $table->json('updated_subtasks')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_assignments', function (Blueprint $table) {
            $table->dropColumn('created_tasks');
            $table->dropColumn('created_subtasks');
            $table->dropColumn('updated_tasks');
            $table->dropColumn('updated_subtasks');
        });
    }
};
