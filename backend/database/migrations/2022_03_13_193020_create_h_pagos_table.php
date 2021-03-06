<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_pagos', function (Blueprint $table) {
            $table->id();
            $table->string('correlativo', 50);
            $table->string('nit', 15)->default('CF');
            $table->string('nombre', 200);
            $table->string('ubicacion', 350)->nullable();
            $table->string('vaucher_pago', 20)->nullable();

            $table->longText('detalle');
            $table->boolean('factura')->default(false);
            $table->decimal('sub_total', 11, 2)->default(0);
            $table->decimal('descuento', 11, 2)->default(0);
            $table->decimal('consumo_restaurante', 11, 2)->default(0);
            $table->decimal('total', 11, 2);
            $table->string('path')->nullable();
            $table->boolean('anulado')->default(false);

            $table->foreignId('h_reservaciones_id')->constrained('h_reservaciones');
            $table->foreignId('tipos_pagos_id')->constrained('tipos_pagos');
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
        Schema::dropIfExists('h_pagos');
    }
}
