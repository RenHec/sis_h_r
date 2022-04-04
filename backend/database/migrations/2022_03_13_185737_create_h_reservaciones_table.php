<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHReservacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_reservaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 15)->unique();
            $table->string('nombre', 200);
            $table->decimal('sub_total', 11, 2); // +
            $table->decimal('extra', 11, 2); // +
            $table->decimal('total', 11, 2); // =
            $table->boolean('reservacion')->default(true);
            $table->boolean('check_in')->default(false);
            $table->boolean('check_out')->default(false);
            $table->boolean('pagado')->default(false);
            $table->boolean('anulado')->default(false);
            $table->string('comprobante')->nullable();
            $table->longText('distribucion')->nullable();
            $table->foreignId('clientes_id')->constrained('clientes');
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
        Schema::dropIfExists('h_reservaciones');
    }
}
