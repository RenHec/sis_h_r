<?php

namespace App\Imports;

use App\Models\V1\Seguridad\Rol;
use App\Models\V1\Seguridad\Menu;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Seguridad\RolMenu;
use Maatwebsite\Excel\Concerns\ToCollection;

class RolMenuImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $ADMINISTRADOR = Rol::create(['nombre' => 'ADMINISTRADOR']);
        foreach ($collection as $key => $value) {
            if ($key != 0) {
                echo $value[0] . PHP_EOL;
                if (!is_null($value[0])) {
                    DB::beginTransaction();
                    $menu = new Menu();
                    $menu->nombre = $value[0];
                    $menu->nombre_ruta = $value[1];
                    $menu->padre = is_null($value[2]) ? 0 : Menu::where('nombre', $value[2])->first()->id;
                    $menu->mostrar = $value[3];
                    $menu->icono = $value[4];
                    $menu->save();

                    $combicacion = $value[5];
                    $roles = array();
                    switch ($combicacion) {
                        case 'ADMIN_GERENTE':
                            array_push($roles, $ADMINISTRADOR->id);
                            break;
                    }

                    foreach ($roles as $value) {
                        $menu_rol = new RolMenu();
                        $menu_rol->rol_id = $value;
                        $menu_rol->menu_id = $menu->id;
                        $menu_rol->save();
                    }
                    DB::commit();
                }
            }
        }
    }
}
