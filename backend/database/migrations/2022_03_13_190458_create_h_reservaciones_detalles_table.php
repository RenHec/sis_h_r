<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHReservacionesDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_reservaciones_detalles', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 10);
            $table->dateTime('inicio')->nullable();
            $table->dateTime('fin')->nullable();
            $table->smallInteger('dias')->default(0);
            $table->smallInteger('horas')->default(0);
            $table->smallInteger('huespedes');
            $table->decimal('precio', 11, 2);
            $table->decimal('sub_total', 11, 2);
            $table->longText('descripcion');
            $table->boolean('disponible')->default(false);
            $table->boolean('incluye_desayuno')->default(false);
            $table->foreignId('h_reservaciones_id')->constrained('h_reservaciones');
            $table->foreignId('h_habitaciones_precios_id')->constrained('h_habitaciones_precios');
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
        Schema::dropIfExists('h_reservaciones_detalles');
    }
}
