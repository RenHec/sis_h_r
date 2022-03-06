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
            $table->string('nit', 15)->default('CF');

            $table->string('nombre', 200)->nullable();

            $table->json('telefonos')->nullable();
            $table->json('emails')->nullable();
            $table->json('direcciones')->nullable();

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
