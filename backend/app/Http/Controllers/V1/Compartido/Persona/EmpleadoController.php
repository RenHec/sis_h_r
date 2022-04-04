<?php

namespace App\Http\Controllers\V1\Compartido\Persona;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\Empleado;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Storage;

class EmpleadoController extends ApiController
{
    private $controlador_principal = 'EmpleadoController';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            return $this->successResponse(Empleado::with('usuario', 'municipio')->withTrashed()->orderByDesc('id')->get());
        } catch (\Throwable $e) {
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@index");
            return $this->errorResponse('Error en el controlador');
        }
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
            DB::beginTransaction();

            $data = $request->all();
            $data['departamento_id'] = Municipio::find($request->municipio_id['id'])->departamento_id;
            $data['municipio_id'] = $request->municipio_id['id'];

            if (!is_null($request->foto)) {
                $img = $this->getB64Image($request->foto);
                $image = Image::make($img);
                $image->encode('jpg', 70);
                $path = "{$request->cui}.jpg";
                Storage::disk('user')->put($path, $image);
                $data['foto'] = $path;
            }

            $persona = Empleado::create($data);

            $this->bitacora_general($persona->getTable(), $this->acciones(0), $persona, "{$this->controlador_principal}@store");
            DB::commit();

            return $this->successResponse('Empleado agregado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\V1\Principal\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function show(Empleado $empleado)
    {
        try {
            DB::beginTransaction();

            $usuario = Usuario::create(
                [
                    'cui' => $empleado->cui,
                    'empleado_id' => $empleado->id,
                    'admin' => Usuario::USUARIO_REGULAR,
                    'password' => $empleado->cui
                ]
            );

            $this->bitacora_general($usuario->getTable(), $this->acciones(0), $usuario, "{$this->controlador_principal}@show");
            DB::commit();

            return $this->successResponse("El usuario es {$empleado->cui} y la contraseña {$empleado->cui}, se recomienda cambiar la contraseña.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@show");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\V1\Principal\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Empleado $empleado)
    {
        $this->validate($request, $this->rules($empleado->id), $this->messages());

        try {
            DB::beginTransaction();
            $empleado->cui = $request->cui;
            $empleado->primer_nombre = $request->primer_nombre;
            $empleado->segundo_nombre = $request->segundo_nombre;
            $empleado->primer_apellido = $request->primer_apellido;
            $empleado->segundo_apellido = $request->segundo_apellido;
            $empleado->email = $request->email;
            $empleado->ubicacion = $request->ubicacion;
            $empleado->telefono = $request->telefono;
            $empleado->departamento_id = Municipio::find($request->municipio_id['id'])->departamento_id;
            $empleado->municipio_id = $request->municipio_id['id'];

            if (!is_null($request->foto)) {
                Storage::disk('user')->exists($empleado->foto) ? Storage::disk('user')->delete($empleado->foto) : null;

                $img = $this->getB64Image($request->foto);
                $image = Image::make($img);
                $image->fit(870, 620, function ($constraint) {
                    $constraint->upsize();
                });
                $image->encode('jpg', 70);
                $path = "{$request->cui}.jpg";
                Storage::disk('user')->put($path, $image);
                $empleado->foto = $path;
            }

            if (!$empleado->isDirty())
                $this->errorResponse('No hay datos para actualizar', 423);

            $empleado->save();
            $this->bitacora_general($empleado->getTable(), $this->acciones(1), $empleado, "{$this->controlador_principal}@update");
            DB::commit();
            return $this->successResponse('Empleado actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\V1\Principal\Empleado  $empleado
     * @return \Illuminate\Http\Response
     */
    public function destroy($empleado)
    {
        try {
            DB::beginTransaction();
            $empleado = Empleado::withTrashed()->find($empleado);
            if (is_null($empleado->deleted_at)) {
                $empleado->delete();
                $message = 'descativado';
            } else {
                $empleado->restore();
                $message = 'activado';
            }
            $this->bitacora_general($empleado->getTable(), $this->acciones(3), $empleado, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Empleado {$message}");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@destroy");
            return $this->errorResponse('Error en el controlador');
        }
    }

    //Reglas de validaciones
    public function rules($id = null)
    {
        $validar = [
            'cui' => is_null($id) ? 'required|numeric|digits_between:13,15|unique:empleados,cui' : "required|numeric|digits_between:13,15|unique:empleados,cui,{$id}",
            'primer_nombre' => 'required|max:50',
            'segundo_nombre' => 'nullable|max:50',
            'primer_apellido' => 'required|max:50',
            'segundo_apellido' => 'nullable|max:50',
            'apellido_casada' => 'nullable|max:50',
            'email' => 'email|max:75',
            'observacion' => 'nullable|max:500',
            'ubicacion' => 'nullable|max:100',
            'telefono' => 'required|max:25',
            'municipio_id.id' => 'required|integer|exists:municipios,id',
            'foto' => 'nullable'
        ];

        return $validar;
    }

    //Mensajes para las reglas de validaciones
    public function messages()
    {
        return [
            'cui.required' => 'El número de CUI es obligatorio.',
            'cui.numeric' => 'El número de CUI tiene formato incorrecto.',
            'cui.digits_between'  => 'El número de CUI ingresado no tiene un mínimo de :min y un máximo de :max dígitos.',
            'cui.unique'  => 'El número de CUI ingreado ya existe en el sistema.',

            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.max'  => 'El primer nombre debe tener menos de :max carácteres.',

            'segundo_nombre.max'  => 'El segundo nombre debe tener menos de :max carácteres.',

            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.max'  => 'El primer apellido debe tener menos de :max carácteres.',

            'segundo_apellido.max'  => 'El segundo apellido debe tener menos de :max carácteres.',

            'apellido_casada.max'  => 'El apellido de casad@ debe tener menos de :max carácteres.',

            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El dato ingresado no es un correo electrónico.',
            'email.max'  => 'El correo electrónico debe tener menos de :max caracteres.',
            'email.unique'  => 'El correo electrónico ingresado ya existe en el sistema.',

            'ubicacion.max'  => 'La ubicación de de tener menos de :max carácteres.',

            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.numeric' => 'El número de teléfono tiene formato incorrecto.',
            'telefono.digits_between'  => 'El número de teléfono ingresado no tiene un 8 dígitos.',
            'telefono.max'  => 'El número de teléfono debe tener menos de :max carácteres.',

            'municipio_id.id.required' => 'El departamento y muicipalidad es obligatorio',
            'municipio_id.id.integer' => 'El departamento y muicipalidad no es un número',
            'municipio_id.id.exists' => 'El departamento y muicipalidad no existe en la base de datos'
        ];
    }
}
