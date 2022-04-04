<?php

namespace App\Http\Controllers\V1\Compartido\Catalogo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use App\Models\V1\Catalogo\Presentacion;
use App\Models\V1\Principal\AsignarPresentacion;

class PresentacionController extends ApiController
{
    private $tabla_principal = 'presentaciones';
    private $controlador_principal = 'PresentacionController';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showAll(Presentacion::todos()->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mensaje = "La presentación {$request->nombre} ya se encuentra registrada en el sistema.";
            $existe = Presentacion::where('nombre', $request->nombre)->first();
            if (is_null($existe)) {
                $presentacion = Presentacion::create(
                    [
                        'abreviatura' => $request->abreviatura,
                        'nombre' => $request->nombre,
                        'usuarios_id' => Auth::user()->id
                    ]
                );

                $mensaje = "La presentación {$presentacion->nombre} fue agregada.";
                $this->bitacora_general($this->tabla_principal, $this->acciones(0), $presentacion, "{$this->controlador_principal}@store");
            }

            DB::commit();

            return $this->successResponse($mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Catalogo\Presentacion  $presentacion
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Presentacion $presentacion)
    {
        try {
            DB::beginTransaction();

            $mensaje = "La presentación {$request->nombre} ya se encuentra registrada en el sistema.";
            $existe = Presentacion::where('nombre', $request->nombre)->first();
            if (is_null($existe)) {
                $presentacion->abreviatura = $request->abreviatura;
                $presentacion->nombre = $request->nombre;

                if (!$presentacion->isDirty())
                    return $this->errorResponse('No hay datos para actualizar', 423);

                $presentacion->save();

                $this->bitacora_general($this->tabla_principal, $this->acciones(1), $presentacion, "{$this->controlador_principal}@update");

                $mensaje = "La presentación {$presentacion->nombre} fue actualizada.";
            }

            DB::commit();

            return $this->successResponse($mensaje);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Catalogo\Presentacion  $presentacion
     * @return \Illuminate\Http\Response
     */
    public function destroy($presentacion)
    {
        try {
            DB::beginTransaction();
            $item = Presentacion::todos()->find($presentacion);
            $message = 'eliminada';
            $item->forceDelete();
        } catch (\Exception $e) {
            if ($e instanceof QueryException) {
                $item = Presentacion::todos()->find($presentacion);
                if (is_null($item->deleted_at)) {
                    $item->delete();
                    $message = 'desactivada';
                } else {
                    $item->restore();
                    $message = 'activada';
                }

                $this->bitacora_general($this->tabla_principal, $message == 'eliminada' ? $this->acciones(2) : $this->acciones(3), $item, "{$this->controlador_principal}@destroy");
                DB::commit();
            } else {
                DB::rollBack();
                return $this->errorResponse('Error en el controlador');
            }

            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
        }
        return $this->successResponse("La presentación {$item->nombre} fue {$message}");
    }
}
