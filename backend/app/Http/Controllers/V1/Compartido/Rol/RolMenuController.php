<?php

namespace App\Http\Controllers\V1\Compartido\Rol;

use Illuminate\Http\Request;
use App\Models\V1\Seguridad\Menu;
use App\Models\V1\Seguridad\RolMenu;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;

class RolMenuController extends ApiController
{
    private $controlador_principal = 'RolMenuController';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules(), $this->messages());
        try {

            foreach ($request->menus_id as $value) {
                if ($value['principal'] > 0) {
                    RolMenu::firstOrCreate(['rol_id' => $request->id, 'menu_id' => $value['principal']]);
                }
                if ($value['padre'] > 0) {
                    RolMenu::firstOrCreate(['rol_id' => $request->id, 'menu_id' => $value['padre']]);
                }
                RolMenu::firstOrCreate(['rol_id' => $request->id, 'menu_id' => $value['id']]);
            }

            return $this->successResponse('Registro agregado');
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Seguridad\RolMenu  $rol_menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(RolMenu $rol_menu)
    {
        try {
            $rol_menu->forceDelete();
            return $this->successResponse('Registro desactivado');
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('El registro se encuentra en uso', 423);
            }
        }
    }

    public function eliminario_masiva(Request $request)
    {
        try {

            foreach ($request->eliminar as $value) {
                RolMenu::find($value['id'])->forceDelete();
            }

            return $this->successResponse('Registro desactivado');
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            if ($e instanceof QueryException) {
                return $this->errorResponse('El registro se encuentra en uso', 423);
            }
        }
    }

    //Reglas de validaciones
    public function rules($id = null)
    {
        $validar = [
            'id' => 'required|integer|exists:roles,id',
            'menus_id.*.id' => 'required|integer|exists:menus,id'
        ];

        return $validar;
    }

    //Mensajes para las reglas de validaciones
    public function messages($id = null)
    {
        return [
            'id.required' => 'El rol es obligatorio',
            'id.integer' => 'El rol no es un n??mero',
            'id.exists' => 'El rol no existe en la base de datos',

            'menus_id.*.id.required' => 'El men?? es obligatorio',
            'menus_id.*.id.integer' => 'El men?? no es un n??mero',
            'menus_id.*.id.exists' => 'El men?? no existe en la base de datos'
        ];
    }
}
