<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHCajaChicaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_caja_chica', function (Blueprint $table) {
            $table->id();

            $table->dateTime('inicio');
            $table->dateTime('finalizo')->nullable();

            $table->decimal('inicia_caja', 8, 2);

            $table->decimal('pagos', 8, 2)->default(0); //reservaciones
            $table->decimal('insumos', 8, 2)->default(0); //insumos - kardex
            $table->decimal('compras', 8, 2)->default(0); //compras no previstas
            $table->decimal('restaurante', 8, 2)->default(0); //compras no previstas

            $table->decimal('cierre_caja', 8, 2)->default(0);

            $table->year('anio');
            $table->foreignId('meses_id')->constrained('meses');
            $table->smallInteger('dia');
            $table->boolean('abierta')->default(true);

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
        Schema::dropIfExists('h_caja_chica');
    }
}
