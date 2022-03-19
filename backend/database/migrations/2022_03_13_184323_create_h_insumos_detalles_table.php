<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHInsumosDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_insumos_detalles', function (Blueprint $table) {
            $table->id();
            $table->string('documento');
            $table->string('producto');
            $table->smallInteger('cantidad');
            $table->decimal('precio', 10, 2);
            $table->decimal('descuento', 10, 2);
            $table->decimal('sub_total', 10, 2);
            $table->foreignId('h_insumos_id')->constrained('h_insumos');
            $table->foreignId('h_productos_id')->constrained('h_productos');
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
        Schema::dropIfExists('h_insumos_detalles');
    }
}
