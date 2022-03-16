<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHKardexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_kardex', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('stock_actual');
            $table->smallInteger('stock_inicial');
            $table->smallInteger('stock_consumido');
            $table->boolean('activo')->default(true);
            $table->foreignId('h_productos_id')->constrained('h_productos');
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
        Schema::dropIfExists('h_kardex');
    }
}
