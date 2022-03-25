<?php

namespace App\Traits;

use App\Models\V1\Catalogo\Municipio;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HKardex;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Principal\CajaPago;
use App\Models\V1\Seguridad\Bitacora;
use Illuminate\Database\Eloquent\Model;
use App\Models\V1\Hotel\HKardexHistorial;
use App\Models\V1\Principal\Cliente;
use App\Models\V1\Principal\Proveedor;

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
	protected function showMessage($message, $code = 210)
	{
		return $this->successResponse($message, $code);
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

	protected function historial_kardex(string $signo, int $stock, int $cantidad, int $id_tabla, HKardex $h_kardex_id, string $producto)
	{
		/*
			$signo, indica el tipo de operación de quede de hacer para registrar el historial
			$stock, es la cantidad de stock actual en el kardex
			$cantidad, es la cantidad que esta sumando o restando en el kardex
			$id_tabla, es el registro ID de la tabla que esta produciendo la acción
			$h_kardex_id, contiene la información del kardex que se encuentra procesando la acción
		*/

		$data['stock_anterior'] = 0;
		$data['signo'] = $signo;
		$data['stock_nuevo'] = $stock;
		$data['h_insumos_detalles_id'] = 0;
		$data['h_check_in_id'] = 0;
		$data['h_kardex_id'] = $h_kardex_id->id;
		$data['usuarios_id'] = Auth::user()->id;

		switch ($signo) {
			case '=':
				$data['documento'] = "Kardex - {$id_tabla}";
				$data['descripcion'] = "Se creo el inventario para el producto {$producto}";
				break;

			case '+':
				$data['documento'] = "Insumo - {$id_tabla}";
				$data['descripcion'] = "Al producto {$producto} se le sumaron {$cantidad}";
				$data['stock_anterior'] = $stock - $cantidad;
				$data['stock_nuevo'] = $stock;
				$data['h_insumos_detalles_id'] = $id_tabla;
				break;

			case 'i':
				$data['documento'] = "Reservacion - {$id_tabla}";
				$data['descripcion'] = "Al producto {$producto} se le restaron {$cantidad}";
				$data['stock_anterior'] = $stock + $cantidad;
				$data['stock_nuevo'] = $stock;
				$data['h_check_in_id'] = $id_tabla;
				break;

			case 'o':
				$data['documento'] = "Reservacion - {$id_tabla}";
				$data['descripcion'] = "Al producto {$producto} se le sumaron {$cantidad}";
				$data['stock_anterior'] = $stock - $cantidad;
				$data['stock_nuevo'] = $stock;
				$data['h_check_in_id'] = $id_tabla;
				break;

			case 'a':
				$data['documento'] = "Insumo - {$id_tabla}";
				$data['descripcion'] = "Al producto {$producto} se le restaron {$cantidad} por anulación";
				$data['stock_anterior'] = $stock + $cantidad;
				$data['stock_nuevo'] = $stock;
				$data['h_insumos_detalles_id'] = $id_tabla;
				break;

			default:
				throw new \Exception("Error al guardar el historial del kardex", 1);
				break;
		}

		HKardexHistorial::create($data);
	}

	protected function generadorCodigo(string $palabra, int $correlativo)
	{
		$codigo = str_pad(strval($correlativo), 5, "0", STR_PAD_LEFT);
		$anio = date('Y');
		return "{$palabra}{$codigo}{$anio}";
	}

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

	protected function cliente_proveedor(Request $request, bool $cliente = true, string $controlador = null)
	{
		if ($cliente) {
			$persona = Cliente::where('nit', $request->nit)->first();
			if (is_null($persona)) {
				$persona = new Cliente();
			}
		} else {
			$persona = Proveedor::where('nit', $request->nit)->first();
			if (is_null($persona)) {
				$persona = new Proveedor();
			}
		}

		$persona->nit = $request->nit;
		$persona->nombre = $request->nombre;
		$persona->telefonos = $request->telefonos;
		$persona->emails = $request->emails;
		$persona->direcciones = $request->direcciones;
		$persona->departamentos_id = Municipio::find($request->municipios_id['id'])->departamento_id;
		$persona->municipios_id = $request->municipios_id['id'];
		$persona->usuarios_id = Auth::user()->id;
		$persona->save();

		return $persona;
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
