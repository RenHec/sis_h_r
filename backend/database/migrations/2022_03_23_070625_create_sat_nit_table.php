<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatNitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sat_nit', function (Blueprint $table) {
            $table->id();
            $table->string('nit');
            $table->string('cui');
            $table->string('primerNombre');
            $table->string('segundoNombre')->nullable();
            $table->string('tercerNombre')->nullable();
            $table->string('primerApellido');
            $table->string('segundoApellido')->nullable();
            $table->string('apellidoCasada')->nullable();
            $table->dateTime('fechaModifico')->nullable();
            $table->integer('correlativo'); //23420084
            $table->smallInteger('digito'); //0
            $table->smallInteger('codigo_departamento'); //06
            $table->smallInteger('codigo_municipio'); //08
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
        Schema::dropIfExists('sat_nit');
    }
}
