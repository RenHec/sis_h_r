<?php

namespace Database\Seeders;

use App\Imports\RolMenuImport;
use App\Models\V1\Catalogo\Mes;
use Illuminate\Database\Seeder;
use App\Imports\MunicipioImport;
use Illuminate\Support\Facades\DB;
use App\Imports\DepartamentoImport;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Seguridad\Usuario;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\V1\Seguridad\UsuarioRol;
use Illuminate\Support\Facades\Artisan;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Principal\Caja;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([RestauranteSeeder::class]);
        //Migrando data para los roles, menus y asociando menu al rol correspondiente
        Excel::import(new RolMenuImport, 'database/seeders/Catalogos/RolMenu.xlsx');

        //Migrando Departamento y Municipios asociados
        Excel::import(new DepartamentoImport, 'database/seeders/Catalogos/Departamentos.xlsx');
        Excel::import(new MunicipioImport, 'database/seeders/Catalogos/Municipios.xlsx');

        Usuario::factory(1)->create();
        Usuario::factory(6)->create();

        UsuarioRol::create([
            'usuario_id' => 2,
            'rol_id' => 1
        ]);

        UsuarioRol::create([
            'usuario_id' => 3,
            'rol_id' => 2
        ]);

        UsuarioRol::create([
            'usuario_id' => 4,
            'rol_id' => 3
        ]);

        UsuarioRol::create([
            'usuario_id' => 5,
            'rol_id' => 1
        ]);

        UsuarioRol::create([
            'usuario_id' => 6,
            'rol_id' => 2
        ]);

        UsuarioRol::create([
            'usuario_id' => 7,
            'rol_id' => 3
        ]);

        Artisan::call('passport:install');

        DB::table('oauth_clients')
            ->where('id', 2)
            ->update(['secret' => 'fb8QYHjGOBKEL4S8CMDNkXW44lSHGmM6lrvoxwO1']);

        $presentacion = Presentacion::create([
            'abreviatura' => 'lib',
            'nombre' => 'LIBRAS',
            'usuarios_id' => 1
        ]);

        Presentacion::create([
            'abreviatura' => '@',
            'nombre' => 'ARROBA',
            'usuarios_id' => 1
        ]);

        Presentacion::create([
            'abreviatura' => 'q',
            'nombre' => 'QUINTAL',
            'usuarios_id' => 1
        ]);

        Mes::create([
            'nombre' => 'ENERO',
        ]);

        Mes::create([
            'nombre' => 'FEBRERO',
        ]);

        Mes::create([
            'nombre' => 'MARZO',
        ]);

        Mes::create([
            'nombre' => 'ABRIL',
        ]);

        Mes::create([
            'nombre' => 'MAYO',
        ]);

        Mes::create([
            'nombre' => 'JUNIO',
        ]);

        Mes::create([
            'nombre' => 'JULIO',
        ]);

        Mes::create([
            'nombre' => 'AGOSTO',
        ]);

        Mes::create([
            'nombre' => 'SEPTIEMBRE',
        ]);

        Mes::create([
            'nombre' => 'OCTUBRE',
        ]);

        Mes::create([
            'nombre' => 'NOVIEMBRE',
        ]);

        Mes::create([
            'nombre' => 'DICIEMBRE',
        ]);

        TipoPago::create([
            'nombre' => 'EFECTIVO'
        ]);

        TipoPago::create([
            'nombre' => 'TARJETA'
        ]);

        Caja::create([
            'inicio' => date('Y-m-d H:i:s'),
            'finalizo' => null,
            'inicia_caja' => 0,
            'venta_total' => 0,
            'compra_total' => 0,
            'creditos' => 0,
            'devolucion' => 0,
            'abierta' => false,
            'meses_id' => date('n'),
            'usuarios_id' => 1
        ]);
    }
}
