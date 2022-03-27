<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHCheckOutTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_check_out', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20);
            $table->string('nombre', 100);
            $table->string('habitacion', 100);
            $table->string('foto', 100); //Guardaremos la imagen en el local storage
            $table->longText('lista');
            $table->longText('descripcion')->nullable();
            $table->foreignId('h_reservaciones_id')->constrained('h_reservaciones');
            $table->foreignId('h_reservaciones_detalles_id')->constrained('h_reservaciones_detalles');
            $table->foreignId('h_check_in_id')->constrained('h_check_in');
            $table->foreignId('usuarios_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('h_check_out');
    }
}
