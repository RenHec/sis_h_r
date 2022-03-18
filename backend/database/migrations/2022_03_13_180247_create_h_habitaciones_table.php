<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHHabitacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_habitaciones', function (Blueprint $table) {
            $table->id();
            $table->string('foto', 100)->nullable(); //Guardaremos la imagen en el local storage
            $table->smallInteger('numero');
            $table->smallInteger('huespedes');
            $table->longText('descripcion');
            $table->foreignId('h_estados_id')->constrained('h_estados');
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
        Schema::dropIfExists('h_habitaciones');
    }
}
