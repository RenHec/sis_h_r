<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHInsumosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_insumos', function (Blueprint $table) {
            $table->id();
            $table->string('documento');
            $table->decimal('sub_total', 10, 2); // +
            $table->decimal('descuento', 10, 2); // -
            $table->decimal('total', 10, 2); // =
            $table->boolean('anulado')->default(false);
            $table->string('nombre_proveedor');
            $table->foreignId('proveedores_id')->constrained('proveedores');
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
        Schema::dropIfExists('h_insumos');
    }
}
