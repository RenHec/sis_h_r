<?php

namespace App\Http\Controllers\V1\Compartido\Usuario;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ApiController;
use App\Models\V1\Principal\Configuracion;
use App\Models\V1\Principal\Empresa;
use App\Models\V1\Seguridad\Rol;
use App\Models\V1\Seguridad\Usuario;
use App\Models\V1\Seguridad\UsuarioRol;

class AuthController extends ApiController
{
    use Authenticatable;

    private $tabla_principal = 'oauth_clients';
    private $controlador_principal = 'AuthController';

    public function __construct()
    {
        $this->middleware('auth:passport')->except(['login']);
    }

    /**
     * @OA\Post(
     *      path="/service/rest/v1/security/auth/login",
     *      operationId="postLogin",
     *      tags={"Autenticación"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Verificación de credenciales de acceso del usuario.",
     *      description="Retorna el objeto del usuario y el token.",
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
    public function login(Request $request)
    {
        $request->validate([
            'cui'       => 'required|numeric|digits_between:13,15|exists:usuarios,cui',
            'password'    => 'required|string',
        ], [
            'cui.required'       => 'El CUI es obligatorio',
            'cui.exists'       => 'El usuario no existe',
            'cui.numeric'       => 'El solo debe contener números',
            'cui.digits_between'       => 'El CUI debe de tener como mínimo :min y máximo :max dígitos',

            'password.required'       => 'La contraseña es obligatoria'
        ]);

        if (!Auth::attempt(['cui' => $request->cui, 'password' => $request->password, 'deleted_at' => null])) {
            return response()->json([
                'error' => 'Las credenciales de acceso no son correctas, vuelva a intentar lo.', 'code' => '401'
            ], 401);
        }

        $http = new Client(
            [
                'verify' => false
            ]
        );

        $response = $http->post(config('services.passport.base_url') . 'servicio/passport/generar/token', [
            'form_params' => [
                'grant_type' => 'password',
                'client_id' => config('services.passport.client_id'),
                'client_secret' => config('services.passport.client_secret'),
                'username' => $request->cui,
                'password' => $request->password,
                'scope' => '*',
            ],
        ]);

        $this->bitacora_general($this->tabla_principal, $this->acciones(4), Usuario::where('cui', $request->cui)->first(), "{$this->controlador_principal}@login");

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * @OA\Get(
     *      path="/service/rest/v1/security/auth/me",
     *      operationId="getUserSesión",
     *      tags={"Autenticación"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Muestra la información del usuario que se encuentra en sesión.",
     *      description="Retorna un objeto del usuario en sesión.",
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
    public function me(Request $request)
    {
        $user = $request->user();
        $buscar_roles = UsuarioRol::select('rol_id')->where('usuario_id', $user->id)->distinct('rol_id')->pluck('rol_id');
        $roles = Rol::whereIn('id', $buscar_roles)->pluck('nombre');

        return $this->successResponse(['user' => $user->empleado, 'roles' => $roles]);
    }

    /**
     * @OA\Get(
     *      path="/service/rest/v1/security/auth/logout",
     *      operationId="getLogout",
     *      tags={"Autenticación"},
     *      security={
     *          {"passport": {}},
     *      },
     *      summary="Muestra la información del usuario que finalizará la sesión.",
     *      description="Retorna un objeto del usuario que finalizó la sesión.",
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
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        $this->bitacora_general($this->tabla_principal, $this->acciones(5), Usuario::find($request->user()->id), "{$this->controlador_principal}@logout");
        return $this->showMessage("saliendo...", 200);
    }
}
