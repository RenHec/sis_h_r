<?php

namespace App\Http\Controllers\V1\Compartido\Persona;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\Proveedor;
use App\Http\Controllers\ApiController;

class ProveedorController extends ApiController
{
    private $controlador_principal = 'ProveedorController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(Proveedor::todos()->get());
        } catch (\Throwable $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Principal\Proveedor  $proveedor_management
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Proveedor $proveedor_management)
    {
        try {
            DB::beginTransaction();
            $proveedor_management->nit = $request->nit;
            $proveedor_management->nombre = $request->nombre;
            $proveedor_management->telefonos = $request->telefonos;
            $proveedor_management->emails = $request->emails;
            $proveedor_management->direcciones = $request->direcciones;
            $proveedor_management->departamentos_id = Municipio::find($request->municipios_id['id'])->departamento_id;
            $proveedor_management->municipios_id = $request->municipios_id['id'];
            $proveedor_management->usuarios_id = Auth::user()->id;
            $proveedor_management->save();

            if (!$proveedor_management->isDirty())
                $this->errorResponse('No hay datos para actualizar', 423);

            $proveedor_management->save();
            $this->bitacora_general($proveedor_management->getTable(), $this->acciones(1), $proveedor_management, "{$this->controlador_principal}@update");
            DB::commit();
            return $this->successResponse('Proveedor actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Principal\Proveedor  $proveedor_management
     * @return \Illuminate\Http\Response
     */
    public function destroy($proveedor_management)
    {
        try {
            DB::beginTransaction();
            $proveedor_management = Proveedor::withTrashed()->find($proveedor_management);
            if (is_null($proveedor_management->deleted_at)) {
                $proveedor_management->delete();
                $message = 'descativado';
            } else {
                $proveedor_management->restore();
                $message = 'activado';
            }
            $this->bitacora_general($proveedor_management->getTable(), $this->acciones(3), $proveedor_management, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Proveedor {$message}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            return $this->errorResponse('Error en el controlador');
        }
    }
}
