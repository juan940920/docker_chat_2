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
        Schema::create('unidads', function (Blueprint $table) {
            $table->id();
            $table->string("nombre", 50);
            $table->string("telefono", 20)->nullable();
            $table->string("interno", 20)->nullable();

            $table->bigInteger("centro_id")->unsigned();
            $table->foreign("centro_id")->references("id")->on("centros");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidads');
    }
};
