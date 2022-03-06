<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCajasPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cajas_pagos', function (Blueprint $table) {
            $table->id();
            $table->decimal('monto_total', 8, 2);

            $table->foreignId('tipos_pagos_id')->constrained('tipos_pagos');
            $table->foreignId('cajas_id')->constrained('cajas');
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
        Schema::dropIfExists('cajas_pagos');
    }
}
