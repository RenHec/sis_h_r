<?php

namespace Database\Seeders;

use App\Imports\RolMenuImport;
use App\Models\V1\Catalogo\Mes;
use Illuminate\Database\Seeder;
use App\Imports\MunicipioImport;
use App\Models\V1\Principal\Caja;
use Database\Seeders\HotelSeeder;
use Illuminate\Support\Facades\DB;
use App\Imports\DepartamentoImport;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Seguridad\Usuario;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\V1\Principal\Proveedor;
use App\Models\V1\Seguridad\UsuarioRol;
use Database\Seeders\RestauranteSeeder;
use Illuminate\Support\Facades\Artisan;
use App\Models\V1\Catalogo\Presentacion;
use App\Imports\ProductoRestauranteImport;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        //Migrando data para los roles, menus y asociando menu al rol correspondiente
        Excel::import(new RolMenuImport, 'database/seeders/Catalogos/RolMenu.xlsx');

        //Migrando Departamento y Municipios asociados
        Excel::import(new DepartamentoImport, 'database/seeders/Catalogos/Departamentos.xlsx');
        Excel::import(new MunicipioImport, 'database/seeders/Catalogos/Municipios.xlsx');

        Usuario::factory(7)->create();

        $usuario = Usuario::where('cui', '0000000000001')->first();
        UsuarioRol::create([
            'usuario_id' => $usuario->id,
            'rol_id' => 1
        ]);

        Artisan::call('passport:install');

        DB::table('oauth_clients')
            ->where('id', 2)
            ->update(['secret' => 'fb8QYHjGOBKEL4S8CMDNkXW44lSHGmM6lrvoxwO1']);

        Presentacion::create([
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
            'nombre' => 'TARJETA',
            'ticket' => 1
        ]);

        Proveedor::factory(100)->create();
        Cliente::factory(100)->create();

        $this->call([HotelSeeder::class]);
        $this->call([RestauranteSeeder::class]);
        Excel::import(new ProductoRestauranteImport, 'database/seeders/Catalogos/Productos.xlsx');
    }
}
