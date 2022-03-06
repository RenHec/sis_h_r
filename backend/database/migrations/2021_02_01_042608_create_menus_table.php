<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Modelo - Controlador
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('nombre_ruta', 100);
            $table->unsignedBigInteger('padre');
            $table->boolean('mostrar')->default(0);
            $table->string('icono', 50)->nullable();
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
        Schema::dropIfExists('menus');
    }
}
