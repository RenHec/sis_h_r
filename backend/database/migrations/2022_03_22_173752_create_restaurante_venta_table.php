<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteVentaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_venta', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('orden_id')->nullable(false);
            $table->bigInteger('tipo_pago_id')->unsigned();
            $table->string('voucher')->nullable();
            $table->bigInteger('cliente_id')->unsigned();
            $table->bigInteger('usuario_id')->unsigned();
            $table->bigInteger('caja_id')->nullable();
            $table->decimal('monto',9,2);
            $table->foreign('orden_id')->references('id')->on('r_orden');
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
        Schema::dropIfExists('r_venta');
    }
}
