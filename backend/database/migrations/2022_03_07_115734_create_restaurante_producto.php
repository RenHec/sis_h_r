<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->decimal('precio', 9, 2);
            $table->decimal('costo', 9, 2)->default(0.00);
            $table->text('descripcion')->nullable();
            $table->string('img');
            $table->boolean('consumo_reservacion')->default(false);
            $table->smallInteger('quien_prepara');
            $table->smallInteger('usa_inventario')->default(0);
            $table->smallInteger('activo')->default(1);
            $table->softDeletes();
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
        Schema::dropIfExists('r_producto');
    }
}
