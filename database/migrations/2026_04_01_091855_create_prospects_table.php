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
        Schema::create('prospects', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('status', ['RDV à prendre', 'Date de RDV', 'OK'])->default('RDV à prendre');
            $table->date('rdv_date')->nullable();
            $table->enum('response_type', ['OUI', 'NON', 'DEVIS'])->nullable();
            $table->string('quote_number')->nullable();
            $table->enum('is_followup', ['OUI', 'NON'])->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prospects');
    }
};
