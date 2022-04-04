<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHCajaChicaMovimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_caja_chica_movimiento', function (Blueprint $table) {
            $table->id();

            $table->string('descripcion', 100);
            $table->decimal('monto_total', 8, 2);

            $table->string('tipo_pago', 15);
            $table->string('comprobante', 15)->nullable();
            $table->boolean('resta')->default(false);
            $table->boolean('registro_manual')->default(false);

            $table->foreignId('usuarios_id')->constrained('usuarios');
            $table->foreignId('h_caja_chica_id')->constrained('h_caja_chica');
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
        Schema::dropIfExists('h_caja_chica_movimiento');
    }
}
