<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteEstadoPedido extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_estado_orden', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('icono');
            $table->smallInteger('inicia')->default(0);
            $table->smallInteger('finaliza')->default(0);
            $table->smallInteger('orden')->default(0);
            $table->string('color');
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
        Schema::dropIfExists('r_estado_orden');
    }
}
