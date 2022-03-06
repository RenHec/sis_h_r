<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();

            $table->dateTime('inicio');
            $table->dateTime('finalizo')->nullable();

            $table->decimal('inicia_caja', 8, 2);
            $table->decimal('venta_total', 8, 2)->default(0);
            $table->decimal('compra_total', 8, 2)->default(0);
            $table->decimal('creditos', 8, 2)->default(0);
            $table->decimal('devolucion', 8, 2)->default(0);
            $table->boolean('abierta')->default(true);

            $table->foreignId('meses_id')->constrained('meses');
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
        Schema::dropIfExists('cajas');
    }
}
