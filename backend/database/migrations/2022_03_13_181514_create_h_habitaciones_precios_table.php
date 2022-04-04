<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHHabitacionesPreciosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_habitaciones_precios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 75);
            $table->decimal('precio_desayuno', 11, 2)->default(0);
            $table->decimal('precio_habitacion', 11, 2);
            $table->decimal('precio', 11, 2);
            $table->boolean('incluye_desayuno')->default(false);
            $table->boolean('activo')->default(true);
            $table->foreignId('h_tipos_camas_id')->constrained('h_tipos_camas');
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
        Schema::dropIfExists('h_habitaciones_precios');
    }
}
