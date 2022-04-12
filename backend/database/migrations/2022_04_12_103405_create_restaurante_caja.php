<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestauranteCaja extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('r_caja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('fecha_apertura');
            $table->string('hora_apertura',15);
            $table->date('fecha_cierre')->nullable();
            $table->string('hora_cierre',15)->nullable();
            $table->decimal('saldo_inicial',9,2);
            $table->decimal('ingresos',9,2)->nullable();
            $table->decimal('egresos',9,2)->nullable();
            $table->smallInteger('activo')->default(1);
            $table->bigInteger('usuario_id')->unsigned();
            $table->foreign('usuario_id')->references('id')->on('usuarios');
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
        Schema::dropIfExists('r_caja');
    }
}
