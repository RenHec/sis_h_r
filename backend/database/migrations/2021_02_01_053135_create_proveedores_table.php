<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 15)->unique();

            $table->string('nombre', 200)->nullable();

            $table->string('telefonos')->nullable();
            $table->string('emails')->nullable();
            $table->string('direcciones')->nullable();

            $table->foreignId('departamentos_id')->constrained('departamentos');
            $table->foreignId('municipios_id')->constrained('municipios');
            $table->foreignId('usuarios_id')->constrained('usuarios');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
}
