<?php

namespace App\Traits;

use App\Models\V1\Catalogo\Estado;
use App\Models\V1\Principal\Stock;
use Illuminate\Support\Collection;
use App\Models\V1\Catalogo\TipoPago;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Principal\Persona;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Principal\CajaPago;
use App\Models\V1\Seguridad\Bitacora;
use App\Models\V1\Catalogo\Movimiento;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Principal\StockVencido;
use Illuminate\Support\Facades\Validator;
use App\Models\V1\Principal\Configuracion;
use App\Models\V1\Principal\StockHistorial;
use App\Models\V1\Seguridad\UsuarioEmpresa;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\V1\Principal\AsignarPresentacion;

trait ApiResponser
{
	protected function successResponse($data, $code = 200)
	{
		return response()->json($data, $code);
	}

	protected function errorResponse($message, $code = 423)
	{
		return response()->json(['error' => $message, 'code' => $code], $code);
	}

	protected function showAll(Collection $collection, $code = 200)
	{
		return response()->json(['data' => $collection], $code);
	}

	protected function showOne(Model $instance, $code = 200, $action = 'SELECT')
	{
		return response()->json(['data' => $instance], $code);
	}

	protected function getB64Image($base64_image)
	{
		// Obtener el String base-64 de los datos         
		$image_service_str = substr($base64_image, strpos($base64_image, ",") + 1);
		// Decodificar ese string y devolver los datos de la imagen        
		$image = base64_decode($image_service_str);
		// Retornamos el string decodificado
		return $image;
	}

	protected function base_datos_message($e)
	{
		$message = "";

		switch ($e->errorInfo[1]) {
			case 4060:
				$message = "No se puede abrir la base de datos solicitada por el inicio de sesión";
				break;
			case 40197:
				$message = "Error en el servicio al procesar la solicitud. Vuelva a intentarlo";
				break;
			case 40501:
				$message = "El servicio está ocupado actualmente. Vuelva a intentar la solicitud después de 10 segundos";
				break;
			case 40613:
				$message = "Vuelva a intentar la conexión más tarde";
				break;
			case 49918:
				$message = "No se puede procesar la solicitud. No hay suficientes recursos para procesar la solicitud";
				break;
			case 49919:
				$message = "No se procesar, crear ni actualizar la solicitud. Hay demasiadas operaciones de creación o actualización en curso para la suscripción";
				break;
			case 1451:
				$message = "El registro se encuentra asociado, no podra realizar la eliminación";
				break;
			case 104:
				$message = "Debe haber elementos ORDER BY en la lista de selección si la instrucción contiene el operador UNION, INTERSECT o EXCEPT";
				break;
			case 107:
				$message = "El prefijo de columna no coincide con un nombre de tabla o un nombre de alias utilizado en la consulta";
				break;
			case 109:
				$message = "Hay más columnas en la instrucción INSERT que valores en la cláusula VALUES. El número de valores de VALUES debe coincidir con el de columnas de INSERT";
				break;
			case 109:
				$message = "Hay más columnas en la instrucción INSERT que valores en la cláusula VALUES. El número de valores de VALUES debe coincidir con el de columnas de INSERT";
				break;
			case 1048:
				$message = "Los campos no pueden ser nulos, debe de especificar un valor";
				break;
			case 42703:
				$message = "No existe una o varias columnas en la BD y que están referenciadas en el modelo";
				break;
			case 1054:
				$message = "No se encuentra la columna en la tabla";
				break;
			case 1241:
				$message = "El operando debe contener 1 columna (s)";
				break;
			default:
				$message = "BD";
				break;
		}

		return $message;
	}

	/*protected function generadorCodigoEmpresa($id)
	{
		$prefijo = Configuracion::find(1);
		$codigo = str_pad(strval($id), 3, "0", STR_PAD_LEFT);
		return "{$prefijo->pre_empresa}{$codigo}";
	}*/

	protected function acciones($key)
	{
		$acciones = [
			'NUEVO', //0
			'EDITAR', //1
			'ELIMINAR', //2
			'CAMBIAR ESTADO', //3
			'INICIO SESION', //4
			'CERRO SESION', //5
			'CAMBIO DE CONTRASEÑA', //6
			'APERTURA DE CAJA', //7
			'CIERRE DE CAJA', //8
			'REPORTE', //9
			'RESTABLER SALDO' //10
		];

		return is_null($key) ? $acciones : $acciones[$key];
	}

	/*protected function crear_cliente_proveedor($persona, $controlador, $request_normal)
	{
		if ($request_normal) {
			$nombre = $persona->representacion == Persona::INDIVIDUAL ? "{$persona->nombres} {$persona->apellidos}" : $persona->completo;
			$persona_existe = Persona::where('completo', $nombre)->first();
			if (is_null($persona_existe)) {
				$persona = Persona::create([
					'representacion' => $persona->representacion,
					'nit' => $persona->nit,
					'nombres' => $persona->nombres,
					'apellidos' => $persona->apellidos,
					'completo' => $nombre,
					'telefono' => $this->traInformacion($persona->telefono) ? $persona->telefono : null,
					'celular' => $this->traInformacion($persona->celular) ? $persona->celular : null,
					'direccion' => $persona->direccion,
					'email' => $this->traInformacion($persona->email) ? $persona->email : null,
					'limite_credito_cliente' => 0,
					'limite_credito_proveedor' => 0,
					'es_proveedor' => $persona->es_proveedor,
					'municipios_id' => $persona->municipios_id['id'],
					'departamentos_id' => $persona->municipios_id['departamento_id'],
					'usuarios_id' => Auth::user()->id
				]);

				$this->bitacora_general('personas', $this->acciones(0), $persona, $controlador);
			} else {
				$persona = $persona_existe;
			}
		} else {
			$nombre = $persona['representacion'] == Persona::INDIVIDUAL ? "{$persona['nombres']} {$persona['apellidos']}" : $persona['completo'];
			$persona_existe = Persona::where('completo', $nombre)->first();
			if (is_null($persona_existe)) {
				$persona = Persona::create([
					'representacion' => $persona['representacion'],
					'nit' => $this->traInformacion($persona['nit']) ? $persona['nit'] : 'CF',
					'nombres' => $persona['nombres'],
					'apellidos' => $persona['apellidos'],
					'completo' => $nombre,
					'limite_credito_cliente' => 0,
					'limite_credito_proveedor' => 0,
					'es_proveedor' => $persona['es_proveedor'],
					'municipios_id' => $persona['municipios_id']['id'],
					'departamentos_id' => $persona['municipios_id']['departamento_id'],
					'usuarios_id' => Auth::user()->id
				]);

				$this->bitacora_general('personas', $this->acciones(0), $persona, $controlador);
			} else {
				$persona = $persona_existe;
			}
		}

		return $persona;
	}*/

	protected function bitacora_general($tabla, $accion, $instance, $controlador, $user = null)
	{
		if (is_null($user)) {
			$user = Auth::user();
		}

		Bitacora::create(
			[
				'tabla' => is_null($tabla) ? ' ' : $tabla,
				'accion' => $accion,
				'descripcion' => $instance,
				'controlador' => $controlador,
				'usuario' => "{$user->primer_nombre} {$user->primer_apellido}",
				'usuarios_id' => $user->id
			]
		);
	}

	protected function registrar_historia_caja($operacion, $caja, $controlador)
	{
		$pago = CajaPago::create([
			'monto_total' => $operacion->total,
			'tipos_pagos_id' => $operacion->tipos_pagos_id,
			'cajas_id' => $caja->id,
			'usuarios_id' => $operacion->usuarios_id
		]);

		$caja->save();

		$this->bitacora_general('cajas_pagos', $this->acciones(0), $pago, $controlador);
	}

	protected function traInformacion($data)
	{
		return (is_null($data) || !isset($data) || str_replace([" ", "null"], "", $data) === "" || $data == "false") ? false : true;
	}

	protected function formatoFecha($fecha)
	{
		return date('Y-m-d', strtotime($fecha));
	}

	protected function formatoMonto($monto)
	{
		return str_replace(",", "", $monto);
	}
}
