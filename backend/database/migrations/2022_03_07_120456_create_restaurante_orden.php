<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteOrden extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_orden', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->decimal('monto', 9, 2);
            $table->bigInteger('estado_orden_id')->unsigned();
            $table->bigInteger('tipo_orden_id')->unsigned();
            $table->bigInteger('mesa_id')->unsigned()->nullable();
            $table->date('fecha');
            $table->string('hora');
            $table->string('codigo_reservacion')->nullable();
            $table->smallInteger('activo')->default(1);
            $table->smallInteger('cuenta_dividida')->default(0);
            $table->bigInteger('usuario_id')->unsigned();
            $table->foreign('estado_orden_id')->references('id')->on('r_estado_orden');
            $table->foreign('tipo_orden_id')->references('id')->on('r_tipo_orden');
            $table->foreign('mesa_id')->references('id')->on('r_mesa');
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
        Schema::dropIfExists('r_orden');
    }
}
