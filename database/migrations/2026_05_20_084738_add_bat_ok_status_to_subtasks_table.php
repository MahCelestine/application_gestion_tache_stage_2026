<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

<<<<<<< HEAD
return new class extends Migration
{
=======
return new class extends Migration {
>>>>>>> 751bd0e07325174818002f5f148742af5a262319
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
<<<<<<< HEAD
            $table->enum('status', ['en cours', 'validé', 'bloqué', 'BAT ok', 'attente BAT'])
=======
            $table->enum('status', ['en cours', 'validé', 'bloqué', 'attente BAT', 'BAT ok'])
>>>>>>> 751bd0e07325174818002f5f148742af5a262319
                ->default('en cours')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subtasks', function (Blueprint $table) {
            //
        });
    }
};
