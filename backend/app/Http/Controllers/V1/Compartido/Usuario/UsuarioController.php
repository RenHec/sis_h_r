<?php

namespace App\Http\Controllers\V1\Compartido\Usuario;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Catalogo\Municipio;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\ApiController;
use App\Models\V1\Principal\Empleado;
use App\Models\V1\Seguridad\UsuarioRol;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends ApiController
{
    private $tabla_principal = 'usuarios';
    private $controlador_principal = 'UsuarioController';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @OA\Get(
     *      path="/service/rest/v1/security/user",
     *      operationId="getUsers",
     *      tags={"Usuario"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Muestra todos los usuarios registrados en la base de datos.",
     *      description="Retorna un array de usuarios.",
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function index()
    {
        $users = Usuario::todos()->get();
        return $this->showAll($users);
    }

    /**
     * @OA\Post(
     *      path="/service/rest/v1/security/user",
     *      operationId="postUsers",
     *      tags={"Usuario"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Crear un nuevo usuario en el sistema.",
     *      description="Retorna el objeto del usuario creado.",
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->rules(), $this->messages());

        try {
            DB::beginTransaction();

            $data = $request->all();
            $data['admin'] = Usuario::USUARIO_REGULAR;
            $data['departamento_id'] = Municipio::find($request->municipio_id['id'])->departamento_id;
            $data['municipio_id'] = $request->municipio_id['id'];
            $data['password'] = base64_decode($request->password);

            if (!is_null($request->foto)) {
                $img = $this->getB64Image($request->foto);
                $image = Image::make($img);
                $image->encode('jpg', 70);
                $path = "{$request->cui}.jpg";
                Storage::disk('user')->put($path, $image);
                $data['foto'] = $path;
            }

            $persona = Empleado::create($data);

            $this->bitacora_general("empleados", $this->acciones(0), $persona, "{$this->controlador_principal}@store");

            $data['empleado_id'] = $persona->id;
            $user = Usuario::create($data);

            $this->bitacora_general($this->tabla_principal, $this->acciones(0), $user, "{$this->controlador_principal}@store");

            foreach ($request->roles as $value) {
                $exite = UsuarioRol::where(
                    'usuario_id',
                    $user->id
                )->where(
                    'rol_id',
                    $value['id']
                )->first();

                if (is_null($exite)) {
                    $rol = UsuarioRol::create(
                        [
                            'usuario_id' => $user->id,
                            'rol_id' => $value['id']
                        ]
                    );

                    $this->bitacora_general('usuarios_roles', $this->acciones(0), $rol, "{$this->controlador_principal}@store");
                }
            }

            DB::commit();

            return $this->successResponse('Registro agregado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@store");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * @OA\Post(
     *      path="/service/rest/v1/security/user_password",
     *      operationId="postUserPassword",
     *      tags={"Usuario"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Cambiar la contraseña del usuario seleccionado.",
     *      description="Retorna el objeto del usuario que cambio la contraseña.",
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *      )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function cambiar_password(Request $request)
    {
        try {
            DB::beginTransaction();
            $user = Usuario::find($request->id);
            $user->password = base64_decode($request->password);
            $user->save();
            $this->bitacora_general($this->tabla_principal, $this->acciones(6), $user, "{$this->controlador_principal}@cambiar_password");
            DB::commit();

            return $this->successResponse('Contraseña actualizada.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@cambiar_password");
            return $this->errorResponse('Error en el controlador', 423);
        }
    }

    /**
     * @OA\Put(
     *      path="/service/rest/v1/security/user/{user}",
     *      operationId="updateUser",
     *      tags={"Usuario"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Actualizar el usuario seleccionado.",
     *      description="Retorna el objeto del usuario actualizado.",
     *      @OA\Parameter(
     *          description="ID del usuario para actualizar",
     *          in="path",
     *          name="user",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function update(Request $request, Usuario $user)
    {
        $this->validate($request, $this->rules($user->empleado_id), $this->messages());

        try {
            DB::beginTransaction();
            $persona = Empleado::find($user->empleado_id);
            $persona->cui = $request->cui;
            $persona->primer_nombre = $request->primer_nombre;
            $persona->segundo_nombre = $request->segundo_nombre;
            $persona->primer_apellido = $request->primer_apellido;
            $persona->segundo_apellido = $request->segundo_apellido;
            $persona->email = $request->email;
            $persona->ubicacion = $request->ubicacion;
            $persona->telefono = $request->telefono;
            $persona->departamento_id = Municipio::find($request->municipio_id['id'])->departamento_id;
            $persona->municipio_id = $request->municipio_id['id'];

            $user->cui = $request->cui;

            if (!is_null($request->foto)) {
                Storage::disk('user')->exists($persona->foto) ? Storage::disk('user')->delete($persona->foto) : null;

                $img = $this->getB64Image($request->foto);
                $image = Image::make($img);
                $image->fit(870, 620, function ($constraint) {
                    $constraint->upsize();
                });
                $image->encode('jpg', 70);
                $path = "{$request->cui}.jpg";
                Storage::disk('user')->put($path, $image);
                $persona->foto = $path;
            }

            if (!$user->isDirty() && !$persona->isDirty())
                $this->errorResponse('No hay datos para actualizar', 423);

            $persona->save();
            $this->bitacora_general("empleados", $this->acciones(1), $persona, "{$this->controlador_principal}@update");
            $user->save();
            $this->bitacora_general($this->tabla_principal, $this->acciones(1), $user, "{$this->controlador_principal}@update");

            DB::commit();
            return $this->successResponse('Registro actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->grabarLog($e->getMessage(), "{$this->controlador_principal}@update");
            return $this->errorResponse('Error en el controlador');
        }
    }

    /**
     * @OA\Delete(
     *      path="/service/rest/v1/security/user/{user}",
     *      operationId="deleteUser",
     *      tags={"Usuario"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Eliminar o cambiar el estado del usuario seleccionado.",
     *      description="Retorna el objeto del usuario eliminado.",
     *      @OA\Parameter(
     *          description="ID del usuario para eliminar",
     *          in="path",
     *          name="user",
     *          required=true,
     *          @OA\Schema(
     *              type="integer",
     *              format="int64",
     *          )
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Respuesta correcta",
     *          @OA\MediaType(
     *           mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="No autenticado",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Permisos denegados"
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Solicitud incorrecta"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Servicio no encontrado"
     *      ),
     *  )
     */
    public function destroy($user)
    {
        try {
            DB::beginTransaction();
            $user = Usuario::withTrashed()->find($user);
            if (is_null($user->deleted_at)) {
                $user->delete();
                $message = 'descativado';
            } else {
                $user->restore();
                $message = 'activado';
            }
            $this->bitacora_general($this->tabla_principal, $this->acciones(3), $user, "{$this->controlador_principal}@destroy");

            DB::commit();
            return $this->successResponse("Registro {$message}");
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
            'password' => is_null($id) ? 'required|min:6' : '',
            'observacion' => 'nullable|max:500',
            'ubicacion' => 'nullable|max:100',
            'telefono' => 'required|max:25',
            'municipio_id.id' => 'required|integer|exists:municipios,id',
            'foto' => 'nullable',
            'roles.*.id' => is_null($id) ? 'required|integer|exists:roles,id' : ''
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

            'password.required' => 'La contraseña es obligatoria.',
            'password.min'  => 'La contraseña debe tener más de :min carácteres.',

            'observacion.max'  => 'La observación de de tener menos de :max carácteres.',

            'ubicacion.max'  => 'La ubicación de de tener menos de :max carácteres.',

            'telefono.required' => 'El número de teléfono es obligatorio.',
            'telefono.numeric' => 'El número de teléfono tiene formato incorrecto.',
            'telefono.digits_between'  => 'El número de teléfono ingresado no tiene un 8 dígitos.',
            'telefono.max'  => 'El número de teléfono debe tener menos de :max carácteres.',

            'municipio_id.id.required' => 'El departamento y muicipalidad es obligatorio',
            'municipio_id.id.integer' => 'El departamento y muicipalidad no es un número',
            'municipio_id.id.exists' => 'El departamento y muicipalidad no existe en la base de datos',

            'roles.*.id.required' => 'El rol es obligatorio',
            'roles.*.id.integer' => 'El rol no es un número',
            'roles.*.id.exists' => 'El rol no existe en la base de datos'
        ];
    }
}
