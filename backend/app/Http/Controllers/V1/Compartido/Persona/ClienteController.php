<?php

namespace App\Http\Controllers\V1\Compartido\Persona;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Principal\Cliente;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Catalogo\Municipio;
use App\Http\Controllers\ApiController;

class ClienteController extends ApiController
{
    private $controlador_principal = 'ClienteController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(Cliente::todos()->get());
        } catch (\Throwable $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Principal\Cliente  $cliente_management
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Cliente $cliente_management)
    {
        try {
            DB::beginTransaction();
            $cliente_management->nit = $request->nit;
            $cliente_management->nombre = $request->nombre;
            $cliente_management->telefonos = $request->telefonos;
            $cliente_management->emails = $request->emails;
            $cliente_management->direcciones = $request->direcciones;
            $cliente_management->departamentos_id = Municipio::find($request->municipios_id['id'])->departamento_id;
            $cliente_management->municipios_id = $request->municipios_id['id'];
            $cliente_management->usuarios_id = Auth::user()->id;
            $cliente_management->save();

            if (!$cliente_management->isDirty())
                $this->errorResponse('No hay datos para actualizar', 423);

            $cliente_management->save();
            $this->bitacora_general($cliente_management->getTable(), $this->acciones(1), $cliente_management, "{$this->controlador_principal}@update");
            DB::commit();
            return $this->successResponse('Cliente actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Principal\Cliente  $cliente_management
     * @return \Illuminate\Http\Response
     */
    public function destroy($cliente_management)
    {
        try {
            DB::beginTransaction();
            $cliente_management = Cliente::withTrashed()->find($cliente_management);
            if (is_null($cliente_management->deleted_at)) {
                $cliente_management->delete();
                $message = 'descativado';
            } else {
                $cliente_management->restore();
                $message = 'activado';
            }
            $this->bitacora_general($cliente_management->getTable(), $this->acciones(3), $cliente_management, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Cliente {$message}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            return $this->errorResponse('Error en el controlador');
        }
    }
}
