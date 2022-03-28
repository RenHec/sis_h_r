<?php

namespace App\Traits;

use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Http\Request;
use App\Models\V1\Hotel\HKardex;
use Illuminate\Support\Collection;
use App\Models\V1\Principal\Cliente;
use Illuminate\Support\Facades\Auth;
use App\Models\V1\Catalogo\Municipio;
use App\Models\V1\Principal\CajaPago;
use App\Models\V1\Seguridad\Bitacora;
use App\Models\V1\Principal\Proveedor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\V1\Hotel\HKardexHistorial;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

trait ApiResponser
{
	private $fpdf;

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

	protected function historial_kardex(string $signo, int $stock, int $cantidad, int $id_tabla, HKardex $h_kardex_id, string $producto, string $tabla, string $titulo, bool $eliminado = false)
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
		$data['tabla'] = $tabla;
		$data['tabla_id'] = $id_tabla;
		$data['h_kardex_id'] = $h_kardex_id->id;
		$data['usuarios_id'] = Auth::user()->id;
		$data['created_at'] = date("Y-m-d H:i:s");
		$data['documento'] = $titulo;
		$data['eliminado'] = $eliminado;

		switch ($signo) {
			case '=': //Solo en el Kardex aplica
				$data['descripcion'] = "Se creo el inventario para el producto {$producto}";
				break;

			case '+i': //Solo aplica al registrar los insumos
				$data['descripcion'] = "Al producto {$producto} se le sumaron {$cantidad} por ingreso de insumo.";
				$data['stock_anterior'] = $stock - $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			case 'ai': //Solo aplica en la anulación de un insumo
				$data['descripcion'] = "Al producto {$producto} se le restaron {$cantidad} por anulación de insumos.";
				$data['stock_anterior'] = $stock + $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			case '-ri': //Solo aplica en el registro de check in de la reservación
				$data['descripcion'] = "Al producto {$producto} se le restaron {$cantidad} por registro de check in.";
				$data['stock_anterior'] = $stock + $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			case 'aci': //Solo aplica en la anulación del check in de la reservación
				$data['descripcion'] = "Al producto {$producto} se le sumaron {$cantidad} por anulación de check in.";
				$data['stock_anterior'] = $stock - $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			case '+ro': //Solo aplica en el registro de check out de la reservación
				$data['descripcion'] = "Al producto {$producto} se le sumaron {$cantidad} por registro de check out.";
				$data['stock_anterior'] = $stock - $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			case 'aco': //Solo aplica en la anulación del check out de la reservación
				$data['descripcion'] = "Al producto {$producto} se le restaron {$cantidad} por anulación de check out.";
				$data['stock_anterior'] = $stock + $cantidad;
				$data['stock_nuevo'] = $stock;
				break;

			default:
				throw new \Exception("Error al guardar el historial del kardex", 1);
				break;
		}

		HKardexHistorial::create($data);
	}

	protected function generadorCodigo(string $palabra, int $correlativo)
	{
		$correlativo = $correlativo === 0 ? 1 : $correlativo + 1;
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

	protected function generarTicket($hotel, $restaurante)
	{
		//https://github.com/AlexanderBV/ticketer
		/*$now = Carbon::now();

		$ticketer = new Ticketer();
		$ticketer->init('dummy');
		$ticketer->setFechaEmision($now);
		$ticketer->setComprobante('COMPROBANTE');
		$ticketer->setSerieComprobante($serie);
		$ticketer->setNumeroComprobante($numero_comprobante);
		$ticketer->setCliente($cliente);
		$ticketer->setTipoDocumento(1);
		$ticketer->setNumeroDocumento($nit);
		$ticketer->setDireccion($direccion);
		$ticketer->setTipoDetalle($tipo_detalle);

		foreach ($hotel->detalle as $agregado) {
			// $nombre, $cantidad, $precio, $icbper, $gratuita
			$ticketer->addItem($agregado['descripcion'], 1, $agregado['sub_total'], false, false);
		}
		$ticketer->addItem("prueba", 1, 2200, false, false);

		$ticketer->setDescuento($hotel->descuento);
		return $ticketer->printComprobante(true, true);*/

		$serie = is_null($restaurante) ? date("Y") : "deivis lo tuyo";
		$numero_comprobante = is_null($restaurante) ? $hotel->correlativo : "deivis lo tuyo";
		$nit = is_null($restaurante) ? $hotel->nit : "deivis lo tuyo";
		$cliente = is_null($restaurante) ? $hotel->nombre : "deivis lo tuyo";
		$direccion = is_null($restaurante) ? $hotel->ubicacion : "deivis lo tuyo";
		$tipo_detalle = is_null($restaurante) ? 'DETALLADO' : 'CONSUMO';
		$total = is_null($restaurante) ? $hotel->total : 'deivis lo tuyo';
		$sub_total = is_null($restaurante) ? $hotel->sub_total : 'deivis lo tuyo';
		$descuento = is_null($restaurante) ? $hotel->descuento : 'deivis lo tuyo';

		$empresa_nombre = "Hotel y Resutante El Mirador";
		$empresa_direccion = "Chiquimulilla, Santa Rosa";

		$qr = Storage::disk('logo')->exists("qr.png");
		if ($qr) {
			$qr = Storage::disk('logo')->path("qr.png");
		} else {
			$qr = QrCode::format('png')->size(100)->generate("Esperamos que vuelva pronto");
			Storage::disk('logo')->put("qr.png", $qr);
			$qr = Storage::disk('logo')->path("qr.png");
		}

		$this->fpdf = new FPDF('P', 'mm', array(80, 180));
		$this->fpdf->AddPage();

		// AUTOR
		$this->fpdf->SetTitle(utf8_decode("Comprobante No.: {$numero_comprobante}"), true);
		$this->fpdf->SetAuthor(utf8_decode($empresa_nombre), true);

		// CABECERA
		$this->fpdf->SetFont(
			'Helvetica',
			'',
			8
		);


		$logo = Storage::disk('logo')->path("logo_ticket.png");


		$this->fpdf->Image($logo, 27, 4, 25, 0, 'PNG');
		$this->fpdf->Ln(7);
		$this->fpdf->Cell(60, 4, utf8_decode($empresa_nombre), 0, 1, 'C');
		$this->fpdf->SetFont('Helvetica', '', 7);
		$this->fpdf->Cell(
			60,
			4,
			utf8_decode($empresa_direccion),
			0,
			1,
			'C'
		);
		$fecha = date('d/m/Y');
		$this->fpdf->SetFont('Helvetica', '', 6);
		$this->fpdf->Cell(60, 4, utf8_decode("Comprobante No: {$numero_comprobante}"), 0, 1, 'C');
		$this->fpdf->Cell(60, 4, "Fecha: {$fecha}", 0, 1, 'C');

		//FACTURA CLIENTE
		$this->fpdf->Ln(2);
		$this->fpdf->SetFont('Helvetica', '', 5);
		$this->fpdf->Cell(60, 4, "NIT: {$nit}", 0, 1, 'L');
		$this->fpdf->MultiCell(60, 4, utf8_decode("Cliente: {$cliente}"), 0, 'J');
		$this->fpdf->MultiCell(60, 4, utf8_decode("Dirección: {$direccion}"), 0, 'J');

		// COLUMNAS
		$this->fpdf->Ln(3);
		$this->fpdf->SetFont(
			'Helvetica',
			'B',
			5
		);
		$this->fpdf->Cell(60, 0, '', 'T', 1);
		$this->fpdf->Cell(30, 4, utf8_decode('Descripción'), 0);
		$this->fpdf->Cell(5, 4, 'Ud', 0, 0, 'R');
		$this->fpdf->Cell(10, 4, 'Precio', 0, 0, 'R');
		$this->fpdf->Cell(15, 4, 'Total', 0, 0, 'R');
		$this->fpdf->Ln(4);
		$this->fpdf->Cell(60, 0, '', 'T');
		$this->fpdf->Ln(0);

		if (!is_null($hotel)) {
			foreach ($hotel->detalle as $value) {
				$this->fpdf->SetFont('Helvetica', '', 5);
				$this->fpdf->MultiCell(30, 4, utf8_decode($value['descripcion']), 0, 'L');
				$this->fpdf->Cell(
					35,
					-5,
					'',
					0,
					0,
					'R'
				);
				$this->fpdf->Cell(
					10,
					-5,
					$value['sub_total'],
					0,
					0,
					'R'
				);
				$this->fpdf->Cell(15, -5, number_format(
					$value['sub_total'],
					2,
					'.',
					','
				), 0, 0, 'R');
				$this->fpdf->Ln(3);
			}
		}

		if (!is_null($restaurante)) {
			//deivis aca va lo tuyo
			foreach ($hotel->detalle as $value) {
				$this->fpdf->SetFont('Helvetica', '', 5);
				$this->fpdf->MultiCell(30, 4, utf8_decode($value['descripcion']), 0, 'L');
				$this->fpdf->Cell(
					35,
					-5,
					'',
					0,
					0,
					'R'
				);
				$this->fpdf->Cell(
					10,
					-5,
					$value['sub_total'],
					0,
					0,
					'R'
				);
				$this->fpdf->Cell(15, -5, number_format(
					$value['sub_total'],
					2,
					'.',
					','
				), 0, 0, 'R');
				$this->fpdf->Ln(3);
			}
		}

		$this->fpdf->Cell(60, 0, '', 'T');

		// SUMATORIO DE LOS PRODUCTOS 
		$this->fpdf->SetFont('Helvetica', '', 6);
		$this->fpdf->Ln(1);
		$this->fpdf->Cell(50, 10, 'SUB TOTAL Q', 0, 0, 'R');
		$this->fpdf->Cell(10, 10, number_format($sub_total, 2), 0, 0, 'R');
		$this->fpdf->Ln(1);
		$this->fpdf->Ln(1);
		$this->fpdf->Ln(1);
		$this->fpdf->Cell(50, 10, 'DESCUENTO Q', 0, 0, 'R');
		$this->fpdf->Cell(10, 10, number_format($descuento, 2), 0, 0, 'R');
		$this->fpdf->Ln(1);
		$this->fpdf->Ln(1);
		$this->fpdf->Ln(1);
		$this->fpdf->Cell(50, 10, 'TOTAL Q', 0, 0, 'R');
		$this->fpdf->Cell(10, 10, number_format($total, 2), 0, 0, 'R');

		// FOOTER
		$this->fpdf->SetY(-25);
		is_null($qr) ? null : $this->fpdf->Image($qr, 27, 100, 25, 0, 'PNG');
		$this->fpdf->SetFont(
			'Helvetica',
			'',
			3
		);
		$this->fpdf->Cell(60, 0, utf8_decode('EL PERIODO DE ANULACIÓN'), 0, 1, 'C');
		$this->fpdf->Ln(2);
		$this->fpdf->Cell(60, 0, utf8_decode("SOLO PUEDE SER APLICADO EL {$fecha}"), 0, 1, 'C');
		$this->fpdf->Ln(2);
		$this->fpdf->SetFont('Arial', 'I', 4);
		$this->fpdf->Cell(
			60,
			0,
			utf8_decode("Página {$this->fpdf->PageNo()}"),
			0,
			1,
			'C'
		);

		return $this->fpdf->Output("S", "PRUEBA.PDF", true);
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
