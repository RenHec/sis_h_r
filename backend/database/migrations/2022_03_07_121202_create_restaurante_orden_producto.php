<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteOrdenProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orden_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->smallInteger('cantidad')->default(1);
            $table->text('notas')->nullable();
            $table->uuid('orden_id')->nullable(false);
            $table->bigInteger('producto_id')->unsigned();
            $table->foreign('producto_id')->references('id')->on('producto');
            $table->foreign('orden_id')->references('id')->on('orden');
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
        Schema::dropIfExists('orden_producto');
    }
}
