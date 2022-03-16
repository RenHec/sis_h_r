<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHHabitacionesFotosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_habitaciones_fotos', function (Blueprint $table) {
            $table->id();
            $table->string('foto', 100)->nullable(); //Guardaremos la imagen en el local storage
            $table->foreignId('h_habitaciones_id')->constrained('h_habitaciones');
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
        Schema::dropIfExists('h_habitaciones_fotos');
    }
}
