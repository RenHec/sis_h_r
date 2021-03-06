<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteProductoCategoriaComida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_producto_categoria_comida', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('categoria_comida_id')->unsigned();
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('categoria_comida_id')->references('id')->on('r_categoria_comida');
            $table->foreign('producto_id')->references('id')->on('r_producto');
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
        Schema::dropIfExists('r_producto_categoria_comida');
    }
}
