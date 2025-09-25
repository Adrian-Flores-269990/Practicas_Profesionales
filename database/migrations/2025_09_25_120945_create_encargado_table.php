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
         Schema::create('encargados', function (Blueprint $table) {
            $table->id('Id_Encargado'); // autoincrement y PK
            $table->string('Nombre', 150);
            $table->integer('RPE')->nullable();
            $table->string('Area', 100)->nullable();
            $table->string('Carrera', 150)->nullable();
            $table->string('Cargo', 100)->nullable();
            $table->string('Correo_Electronico', 150)->unique();
            $table->string('Telefono', 20)->nullable();
            $table->string('Contrasena', 255);
            $table->timestamps(); // crea created_at y updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encargado');
    }
};
