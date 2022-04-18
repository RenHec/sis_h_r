<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHKardexHistorialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('h_kardex_historial', function (Blueprint $table) {
            $table->id();
            $table->string('documento', 100);
            $table->string('tabla', 50);
            $table->smallInteger('stock_anterior');
            $table->char('signo', 4);
            $table->smallInteger('stock_nuevo');
            $table->text('descripcion');
            $table->boolean('eliminado')->default(false);
            $table->unsignedBigInteger('tabla_id')->nullable();
            $table->foreignId('h_kardex_id')->constrained('h_kardex');
            $table->foreignId('usuarios_id')->constrained('usuarios');
            $table->unsignedBigInteger('h_habitaciones_id')->default(0);
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
        Schema::dropIfExists('h_kardex_historial');
    }
}
