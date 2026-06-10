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
        Schema::table('tasks', function (Blueprint $table) {
<<<<<<< HEAD
            $table->enum('status', ['en cours', 'validé', 'bloqué', 'BAT ok', 'attente BAT'])
                ->default('en cours')
                ->change();
=======
            $table->enum('status', ['en cours', 'validé', 'bloqué', 'attente BAT', 'BAT ok'])
              ->default('en cours')
              ->change();
>>>>>>> 751bd0e07325174818002f5f148742af5a262319
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
<<<<<<< HEAD
            //
=======
            $table->enum('status', ['en cours', 'validé', 'bloqué', 'attente BAT', 'BAT ok'])
              ->default('en cours')
              ->change();
>>>>>>> 751bd0e07325174818002f5f148742af5a262319
        });
    }
};
