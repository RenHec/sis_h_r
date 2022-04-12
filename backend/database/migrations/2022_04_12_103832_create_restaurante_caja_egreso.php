<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteCajaEgreso extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_caja_egreso', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('descripcion');
            $table->decimal('monto',9,2);
            $table->string('comprobante');
            $table->bigInteger('caja_id')->unsigned();
            $table->bigInteger('usuario_id')->unsigned();
            $table->foreign('caja_id')->references('id')->on('r_caja');
            $table->foreign('usuario_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('r_caja_egreso');
    }
}
