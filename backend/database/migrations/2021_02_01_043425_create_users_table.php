<?php


use App\Models\V1\Seguridad\Usuario;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Modelo - Controlador
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('cui', 15)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('admin')->default(Usuario::USUARIO_REGULAR);

            $table->foreignId('empleado_id')->constrained('empleados');

            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('usuarios');
    }
}
