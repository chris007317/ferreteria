<?php 

namespace Controllers;

use MVC\Router;

use Model\Producto;
use Model\TablaTabla;
use Model\Persona;
use Model\Cliente;
use Model\Venta;
use Model\MovimientoInventario;
use Model\SerieVenta;
use Model\VentaDetalle;

use Response\ListaProductosResponse;
use Response\ProductoVentaResponse;
use Response\MovimientoInventarioVentaResponse;

use Enum\EstadoRegistro;
use Enum\EstadoProducto;
use Enum\Codigos;
use Enum\TipoPago;
use Enum\TipoDocumentoPersona;
use Enum\TipoMovimiento;

Class VentaController {
	public static function Index(Router $router){
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$estadoProducto = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$respuesta['tiposDocumentos'] = TablaTabla::whereAll('grupo', Codigos::TIPO_DOCUMENTO->value);
		$respuesta['tiposVenta'] = TablaTabla::whereAll('grupo', Codigos::TIPO_VENTA->value);
		$respuesta['productos'] = Producto::ListarProductosActivos(
			ListaProductosResponse::Class,
			$estadoRegistro->getIdTabtab(),
			$estadoProducto->getIdTabtab()
		);
		$router->render('/ventas/index',[
			'titulo' => 'Nueva venta',
			'script' => 'ventas',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$tipoDocumento  = trim($_POST['cmbTipoCliente']);
		$tipoPago = trim($_POST['cmbTipoPago']);
		if(!TipoPago::esValido($tipoPago) || !TipoDocumentoPersona::esValido($tipoDocumento)){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$tipoDocumentoRespuesta = TablaTabla::where('codigo', $tipoDocumento);
		$tipoPagoRespuesta = TablaTabla::where('codigo', $tipoPago);
		$estadoProducto = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		if(!$tipoDocumentoRespuesta || !$tipoPagoRespuesta){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$args['id_tipo_doc'] = $tipoDocumentoRespuesta->getIdTabtab();
		$args['num_documento'] = $_POST['txtNumeroDocumento'];
		$args['nombres'] = trim($_POST['txtNombresPersona']);
		$args['apellidos'] = trim($_POST['txtApellidosPersona']);
		if(!isset($_POST['productos']) || empty($_POST['productos'])){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Debe seleccionar por lo menos un producto para la venta.']
	        ]);
	        return;
		}
		$idUsuario = $_SESSION['usuario_id'];
		$persona = Persona::where('num_documento', $args['num_documento']);
		$cliente = null;
		if(!$persona){
			$persona = new Persona($args);
			$alertas = $persona->validar();
			if(!empty($alertas)){
				echo json_encode(['alerta'=>[$alertas][0]]);
				return;	
			}
			$personaRespuesta = $persona->guardar();
			if($personaRespuesta && $personaRespuesta['resultado']){
				$args['id_persona'] = $personaRespuesta['id'];
			}
			else{
		        echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'No se pudo registrar los datos de la persona.']
	            ]);
	            return;
			}			
			return;
		}else{
			$args['id_persona'] = $persona->getIdPersona();
		}
		$cliente = Cliente::where('id_cliente_persona', $args['id_persona']);
		if(!$cliente){
			$cliente = new Cliente(['id_cliente_persona' => $args['id_persona']]);
			$respuestaCliente = $cliente->guardar();
			if(!$respuestaCliente || (isset($respuestaCliente['resultado']) && !$respuestaCliente['resultado'])) {
				echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'No se pudo registrar los datos de la persona.']
	            ]);
	            return;
			}
			$cliente->setIdCliente($respuestaCliente['id']);
		}		
		$productos = json_decode($_POST['productos'], true);
	    if ($productos === null || !is_array($productos)) {
	        echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'El listado de productos es inválido.']
	        ]);
	        return;
	    }
	    $idsProductos = array_column($productos, 'idProducto');
	    $idsUnicos = array_unique($idsProductos);
	    if (count($idsProductos) !== count($idsUnicos)) {
	        $duplicados = array_diff_assoc($idsProductos, array_unique($idsProductos));
	        echo json_encode([
	            'alerta' => [
	                'tipo' => 'error', 
	                'mensaje' => 'Productos duplicados: ' . implode(', ', array_unique($duplicados))
	            ]
	        ]);
	        return;
	    }
	    $inCondiciones['id_producto'] = $idsProductos;
	    $andCondiciones = [
	        'id_estado_producto' => $estadoProducto->getIdTabtab(),
	        'producto_eliminado' => FALSE
	    ];
	    $productosSeleccionados = Producto::whereAndIn($andCondiciones, $inCondiciones);
	    $mapaProductos = [];
	    foreach ($productosSeleccionados as $producto) {
	        $mapaProductos[$producto->getIdProducto()] = $producto;
	    }	    
		$igv = 0.18;
		$totalVenta = 0;
		$detalleVenta = [];
		$movimientosData = [];
		$tipoMovimiento = TablaTabla::where('codigo', TipoMovimiento::SALIDA->value);
		foreach ($productos as $key => $producto) {
         	$idProducto = $producto['idProducto'];
			if (!isset($mapaProductos[$idProducto])) {
		        echo json_encode([
		            'alerta' => ['tipo' => 'warning', 'mensaje' => "{$producto['textoProducto']} no se encuentra disponible"]
		        ]);
		        return;
			}
			$productoValidar = $mapaProductos[$idProducto];
	        $cantidadSolicitada = $producto['txtCantidadProducto'];
	        if ($cantidadSolicitada <= 0) {
		        echo json_encode([
		            'alerta' => ['tipo' => 'warning', 'mensaje' => "La cantidad para '{$productoValidar->getNombre()}' debe ser mayor a cero"]
		        ]);
		        return;
	        }	        
	        $stockDisponible = $productoValidar->getStock();
	        if ($stockDisponible <= 0) {
		        echo json_encode([
		            'alerta' => ['tipo' => 'warning', 'mensaje' => "El producto '{$productoValidar->getNombre()}' no tiene stock disponible"]
		        ]);
		        return;
	        }
	        if ($stockDisponible < $cantidadSolicitada) {
		        echo json_encode([
		            'alerta' => ['tipo' => 'warning', 'mensaje' => "Stock insuficiente para '{$productoValidar->getNombre()}'. Disponible: {$stockDisponible}, Solicitado: {$cantidadSolicitada}"]
		        ]);
		        return;
	        }	        	        
	        $total = round($cantidadSolicitada * $producto['precioUnit'],2);
	        $totalDescuento = round($producto['descuento'] * $cantidadSolicitada,2);
			$subTotal = round($total * $igv, 2);
			$productoVenta = new ProductoVentaResponse();
			$productoVenta->id_producto = $producto['idProducto'];
			$productoVenta->precio_unitario = $producto['precioUnit'];
			$productoVenta->cantidad = $producto['txtCantidadProducto'];
			$productoVenta->descuento_unidad = $producto['descuento'];
			$productoVenta->sub_total = $subTotal;
			$productoVenta->igv = $igv;
			$productoVenta->precio_descuento = $totalDescuento;
			$detalleVenta[] = $productoVenta;
			$totalVenta = ($totalVenta - $totalDescuento) + $producto['total'];
			$movimiento = new MovimientoInventarioVentaResponse();
			$movimiento->id_tipo_movimiento = $tipoMovimiento->getIdTabtab();
			$movimiento->id_producto = $idProducto;
			$movimiento->id_almacen = $productoValidar->getIdAlmacenPrincipal();
			$movimiento->id_usuario = $idUsuario;
			$movimiento->id_cantidad = $cantidadSolicitada;
			$movimiento->id_stock_anterior = $productoValidar->getStock();
			$productoValidar->nuevoEstock($cantidadSolicitada);
			$movimiento->id_stock = $productoValidar->getStock();
			$movimientosData[] = $movimiento;
		}
		$argsVenta['id_cliente'] = $cliente->getIdCliente();
		$argsVenta['id_usuario'] = $idUsuario;
		$argsVenta['id_tipo_pago'] = $tipoPagoRespuesta->getIdTabtab();
		$argsVenta['descuento'] = $_POST['txtDescuento'];
		$argsVenta['total'] = $total;
		$venta = new Venta($argsVenta);
		$alertas = $venta->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		Venta::beginTransaction();
		try{
			$serieVenta = self::GenerarNumeroVenta("V001");
			$venta->setNumeroVenta($serieVenta->GenerarNumeroVenta());
			$venta->calcularComplementario();
			$respuestaVenta = $venta->guardar();
			if(!$respuestaVenta || (isset($respuestaVenta['resultado']) && !$respuestaVenta['resultado']))
				throw new Exception("No se pudo emitir la venta correctamente");
			$respuestaVentaDetalle = (new VentaDetalle())->InsertarVariosDetalle($detalleVenta, $respuestaVenta['id']);
			if(!$respuestaVentaDetalle) 
				throw new Exception("No se pudo emitir la venta correctamente");
			$actualizarProducto = (new Producto)->updateAllObjects($mapaProductos);
			if(!$actualizarProducto)
				throw new Exception("Ocurrio un error al realizar la acción");
			$respuesta = (new MovimientoInventario())->InsertarVariosMovimientosVenta($movimientosData, $respuestaVenta['id']);
			if(!$respuesta) 
				throw new Exception("No se pudo emitir la venta correctamente");
			Venta::commit();
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'La venta fue registrada con exito']]) ;				
		}catch(Exception $e){
			Venta::rollBack();
		    echo json_encode([
		        'alerta' => ['tipo' => 'error', 'mensaje' => $e->getMessage()]
		    ]);
		}
	}

	private static function GenerarNumeroVenta($serie = 'B001'){
		$serieBoleta = SerieVenta::where('serie', $serie);
		if(!$serieBoleta){
			$serieBoleta = new SerieVenta();
			$serieBoleta->setYear(date('Y'));
			$serieBoleta->setSerie($serie);
			$serieBoleta->setUltimoCorrelativo(1);
		}else{
	        $serieBoleta->incrementarCorrelativo();
        	$respuesta = $serieBoleta->guardar();
		}
		$respuesta = $serieBoleta->guardar();
		if ((isset($respuesta['resultado']) && $respuesta['resultado']) || $respuesta) {
			return $serieBoleta;
		}
		return null;
	}	
}