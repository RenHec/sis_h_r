<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteTipoProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /* Schema::create('tipo_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('icono');
            $table->smallInteger('orden')->default(0);
            $table->timestamps();
        }); */
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tipo_producto');
    }
}