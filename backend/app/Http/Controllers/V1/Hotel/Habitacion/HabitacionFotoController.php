<?php

namespace App\Http\Controllers\V1\Hotel\Habitacion;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Hotel\HHabitacion;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HHabitacionFoto;
use Intervention\Image\Exception\ImageException;

class HabitacionFotoController extends ApiController
{
    private $controlador_principal = 'HabitacionFotoController';

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Hotel\HHabitacion  $habitacion_foto
     * @return \Illuminate\Http\Response
     */
    public function show(HHabitacionFoto $habitacion_foto)
    {
        try {
            $habitacion = HHabitacion::find($habitacion_foto->h_habitaciones_id);
            $habitacion->foto = $habitacion_foto->foto;
            $habitacion->save();
            return $this->successResponse("Fotografía principal actualizada.");
        } catch (\Exception $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@show");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Hotel\HHabitacion  $habitacion_foto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, HHabitacion $habitacion_foto)
    {
        try {
            DB::beginTransaction();
            foreach ($request->fotografias as $value) {
                if (isset($value['foto']) && !is_null($value['foto']) && !empty($value['foto'])) {

                    $img = $this->getB64Image($value['foto']);
                    $image = Image::make($img);
                    $image->encode('jpg', 70);
                    $nombre = Str::random(10);
                    $path = "{$nombre}.jpg";
                    Storage::disk('habitacion')->put($path, $image);

                    $fotografia = HHabitacionFoto::create(['foto' => $path, 'h_habitaciones_id' => $habitacion_foto->id]);

                    $this->bitacora_general($fotografia->getTable(), $this->acciones(0), $fotografia, "{$this->controlador_principal}@update");
                }
            }
            DB::commit();
            return $this->successResponse("Fotografías para la habitación: {$habitacion_foto->description} fueron agregadas.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            if ($e instanceof ImageException) {
                return $this->errorResponse('Ocurrio un problema al grabar la fotografía de la habitación');
            } else if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al guardar la fotografía de la habitación.');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Hotel\HHabitacionFoto  $habitacion_foto
     * @return \Illuminate\Http\Response
     */
    public function destroy(HHabitacionFoto $habitacion_foto)
    {
        try {
            DB::beginTransaction();
            Storage::disk('habitacion')->exists($habitacion_foto->foto) ? Storage::disk('habitacion')->delete($habitacion_foto->foto) : null;
            $habitacion_foto->delete();
            $this->bitacora_general($habitacion_foto->getTable(), $this->acciones(0), $habitacion_foto, "{$this->controlador_principal}@destroy");
            DB::commit();
            return $this->successResponse("Fotografía eliminada.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            if ($e instanceof QueryException) {
                return $this->errorResponse('Ocurrio un problema al eliminar la fotografía de la habitación.');
            }
            return $this->errorResponse('Error en el controlador');
        }
    }
}
