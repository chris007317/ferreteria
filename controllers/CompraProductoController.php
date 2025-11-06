<?php 

namespace Controllers;

use MVC\Router;

use Model\Compra;
use Model\Proveedor;
use Model\TablaTabla;
use Model\Categoria;
use Model\Almacen;
use Model\Producto;
use Model\CompraDetalle;
use Model\MovimientoInventario;

use Response\BandejaCompraProductoResponse;

use Enum\EstadoRegistro;
use Enum\EstadoCompra;
use Enum\EstadoProducto;
use Enum\TipoMovimiento;

Class CompraProductoController {
	public static function Index(Router $router){
		$numeroCompra = trim($_GET['numero_compra']);
		if(empty($numeroCompra)){
			return;
		}
		$respuesta['compra'] = Compra::where('numero_compra', $numeroCompra);
		if(!$respuesta['compra']){
			return;
		}
		$respuesta['estado'] = TablaTabla::where('id_tabtab', $respuesta['compra']->getIdEstadoCompra());
		$respuesta['proveedor'] = Proveedor::where('id_proveedor', $respuesta['compra']->getIdProveedor());
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$respuesta['almacenes'] = Almacen::whereAll('id_estado_almacen', $estadoRegistro->getIdTabtab());
		$respuesta['categorias'] = Categoria::whereAll('id_estado_categoria', $estadoRegistro->getIdTabtab());
		$respuesta['productos'] = Producto::ListarProductoCompra(
			BandejaCompraProductoResponse::Class,
			$respuesta['compra']->getIdCompra()
		);
		$router->render('/compras/productos',[
			'titulo' => 'Productos',
			'script' => 'compras-productos',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idCompra = trim($_POST['idCompra']);
		$args['id_almacen_principal'] = trim($_POST['cmbAlmacenProducto']);
		$args['id_categoria'] = trim($_POST['cmbCategoriaProducto']);
		$args['nombre'] = trim($_POST['cmbNombreProducto']);
		$args['costo_unitario'] = floatval($_POST['txtPrecioCompraProducto']);
		$args['precio_unitario'] = floatval($_POST['txtPrecioVentaProducto']);
		$args['descripcion'] = trim($_POST['txtDescripcionProducto']);
		$args['cantidad'] = intval($_POST['txtCantidadProducto']);
		if(!filter_var($args['id_almacen_principal'], FILTER_VALIDATE_INT) || !filter_var($args['id_categoria'], FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El almacén o categóría seleccionado es invalido']]);
			return;
		}
		$estadoCompra = TablaTabla::where('codigo', EstadoCompra::PENDIENTE->value);
		$compra = Compra::whereArrayOne([
			'id_compra' => $idCompra,
			'id_estado_compra' => $estadoCompra->getIdTabtab()
		]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La compra no adminte adición de productos']]);
			return;
		}
		$estadoProducto = TablaTabla::where('codigo', EstadoProducto::PENDIENTE->value);
		$args['id_estado_producto'] = $estadoProducto->getIdTabtab();
		$producto = new producto($args);
		$alertas = $producto->Validar();
		if($alertas){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$productoExiste = producto::ExisteRegistro([
			'nombre' => $args['nombre'],
			'id_almacen_principal' => $args['id_almacen_principal'],
			'id_categoria' => $args['id_categoria'],
			'producto_eliminado' => FALSE
		]);
		if($productoExiste){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ya existe un producto con el mismo nombre en la misma categoría y almacén']]);
			return;
		}
		Producto::beginTransaction();
		try{
			$producto->setStock(0);
			$respuesta = $producto->guardar();
			$compraDetalle = new CompraDetalle($args);
			$compraDetalle->setIdCompra($idCompra);
			if(!$respuesta || (isset($respuesta['resultado']) && !$respuesta['resultado'])) throw new Exception("Ocurrio un error al crear el producto");
			$compraDetalle->setIdProducto($respuesta['id']);
			$respuestaCompra = $compraDetalle->guardar();
			$compra->ActualizarDatosCompra();
			if(!$respuestaCompra || (isset($respuestaCompra['resultado']) && !$respuestaCompra['resultado'])) throw new Exception("Ocurrio un error al crear el producto");
			Producto::commit();
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'detalle de la compra registrado con exito']]) ;
			return;
		}catch(Exception $e){
			Producto::rollBack();
		    echo json_encode([
		        'alerta' => ['tipo' => 'error', 'mensaje' => $e->getMessage()]
		    ]);
		    return;
		}
	}

	public static function AprobarCompra()	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$estadoNuevoCompra = TablaTabla::where('codigo', EstadoCompra::APROBADO->value);
		$estadoActualProducto = TablaTabla::where('codigo', EstadoProducto::PENDIENTE->value);
		$estadoNuevoProducto = TablaTabla::where('codigo', EstadoProducto::REGISTRADO->value);
		$idCompra = $_POST['idAccion'];
		if(!filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Categoría no encontrado']]);
			return;
		}
		$compra = Compra::existeRegistro(['id_compra' => $idCompra]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La categoría seleccionada no fue encontrado']]);
			return;
		}
		$compra->setIdEstadoCompra($estadoNuevoCompra->getIdTabtab());
		$productos = Producto::ListarProductoCompra(
			BandejaCompraProductoResponse::Class,
			$idCompra
		);
		if (!$productos) {
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'debe insertar por lo menos un producto']]);
			return;
		}
		$productosPendientes = array_filter($productos, function($p) use ($estadoActualProducto){
			return $p->id_estado_producto == $estadoActualProducto->getIdTabtab();
		});
		Compra::beginTransaction();
		try{
			$respuesta = $compra->updateOnly();
			if(!$respuesta) throw new Exception("Ocurrio un error al crear el producto");
			if (!empty($productosPendientes)) {
				$idsPendientes = array_map(fn($p) => $p->id_producto, $productosPendientes);
				$idsString = implode(",", $idsPendientes);
				$respuestaProducto = Producto::EditarEstadoProductosCompra(
					$estadoNuevoProducto->getIdTabtab(),
					$estadoActualProducto->getIdTabtab(),
					$idsString
				);
			}
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Detalle de la compra fue aprobada con exito']]) ;			
			Compra::commit();
		}catch(Exception $e){
			Compra::rollBack();
		    echo json_encode([
		        'alerta' => ['tipo' => 'error', 'mensaje' => $e->getMessage()]
		    ]);
		    return;
		}
	}

	public static function RecibirCompra(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$estadoNuevoCompra = TablaTabla::where('codigo', EstadoCompra::RECIBIDO->value);
		$estadoActualProducto = TablaTabla::where('codigo', EstadoProducto::REGISTRADO->value);
		$estadoNuevoProducto = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$tipoMovimiento = TablaTabla::where('codigo', TipoMovimiento::ENTRADA->value);
		$idCompra = $_POST['idAccion'];
		if(!filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Compra no encontrado']]);
			return;
		}
		$compra = Compra::existeRegistro(['id_compra' => $idCompra]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La compra seleccionada no fue encontrado']]);
			return;
		}
		$compra->setIdEstadoCompra($estadoNuevoCompra->getIdTabtab());
		$productos = Producto::ListarProductoCompra(
			BandejaCompraProductoResponse::Class,
			$idCompra
		);
		if (!$productos) {
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'debe insertar por lo menos un producto']]);
			return;
		}
		$productosRegistrados = array_filter($productos, function($p) use ($estadoActualProducto){
			return $p->id_estado_producto == $estadoActualProducto->getIdTabtab();
		});
		Compra::beginTransaction();
		$movimientosData = [];
		$productosActualizar = [];
		try {
			foreach($productos as $key => $producto){
				$actualizarProducto = new Producto(['id_producto' => $producto->id_producto]);
				$actualizarProducto->setStock($producto->stock + $producto->cantidad);
				$precioVenta = (($producto->stock * $producto->precio_venta) + ($producto->cantidad * $producto->precio_unitario)) / $actualizarProducto->getStock();
				$actualizarProducto->setPrecioVenta($precioVenta);
				$productosActualizar[] = $actualizarProducto;
				// if(!$actualizarProducto->updateOnly()) throw new Exception("Ocurrio un error al realizar la acción");
			    $movimientosData[] = [
			        $tipoMovimiento->getIdTabtab(),
			        $producto->id_producto,
			        $producto->id_almacen_principal,
			        $idCompra,
			        $_SESSION['usuario_id'],
			        $producto->cantidad,
			        $producto->stock,
			        $producto->stock + $producto->cantidad
			    ];				
			}
			$actualizarProducto = (new Producto)->updateAllObjects($productosActualizar);
			if(!$actualizarProducto) throw new Exception("Ocurrio un error al realizar la acción");
			$movimiento = new MovimientoInventario();
			$respuestaMovimiento = $movimiento->InsertarVariosMovimientos($movimientosData);
			if(!$respuestaMovimiento) throw new Exception("Ocurrio un error al realizar la acción");
			$respuesta = $compra->updateOnly();
			if(!$respuesta) throw new Exception("Ocurrio un error al actualizar el producto");
			if (!empty($productosRegistrados)) {
				$idsRegistrados = array_map(fn($p) => $p->id_producto, $productosRegistrados);
				$idsString = implode(",", $idsRegistrados);
				$respuestaProducto = Producto::EditarEstadoProductosCompra(
					$estadoNuevoProducto->getIdTabtab(),
					$estadoActualProducto->getIdTabtab(),
					$idsString
				);
			}
			Compra::commit();
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Detalle de la compra fue aprobada con exito']]) ;			
		} catch (Exception $e) {
			Compra::rollBack();
		    echo json_encode([
		        'alerta' => ['tipo' => 'error', 'mensaje' => $e->getMessage()]
		    ]);
		}
	    return;
	}

	public static function SeleccionarPorCategoriaAlmacen(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idCategoria = $_GET['idCategoria'];
		$idAlmacen = $_GET['idAlmacen'];
		if(!filter_var($idCategoria, FILTER_VALIDATE_INT) || 
			!filter_var($idAlmacen, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Categoría o almacén no encontrado']]);
			return;
		}
		$estadoRegistrado = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$producto = Producto::whereArrayAll([
			'id_categoria' => $idCategoria,
			'id_almacen_principal' => $idAlmacen,
			'id_estado_producto' => $estadoRegistrado->getIdTabtab(),
			'producto_eliminado' => FALSE
		]);
		echo !$producto ?
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ingrese nuevo producto']]) :
			json_encode(['alerta' => ['tipo' => 'ok'], 'datos' => $producto]);
		return;
	}

	public static function AgregarProducto(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idCompra = trim($_POST['idCompra']);
		$idProducto = trim($_POST['cmbNombreProducto']);
		$args['precio_unitario'] = floatval($_POST['txtPrecioVentaProducto']);
		$args['costo_unitario'] = floatval($_POST['txtPrecioCompraProducto']);
		$args['cantidad'] = intval($_POST['txtCantidadProducto']);
		$args['descripcion'] = trim($_POST['txtDescripcionProducto']);
		if(!filter_var($idProducto, FILTER_VALIDATE_INT) || !filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El almacén o categóría seleccionado es invalido']]);
			return;
		}
		$estadoCompra = TablaTabla::where('codigo', EstadoCompra::PENDIENTE->value);
		$compra = Compra::whereArrayOne([
			'id_compra' => $idCompra,
			'id_estado_compra' => $estadoCompra->getIdTabtab()
		]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La compra no adminte adición de productos']]);
			return;
		}
		$estadoRegistrado = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$producto = Producto::ExisteRegistro([
			'id_producto' => $idProducto,
			'id_estado_producto' => $estadoRegistrado->getIdTabtab(),
			'producto_eliminado' => FALSE
		]);
		if(!$idProducto){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'No existe el producto seleccionado']]);
			return;
		}
		$args['id_producto'] = $idProducto;
		$args['id_compra'] = $idCompra;
		$compraDetalle = new CompraDetalle($args);
		$respuesta = $compraDetalle->guardar();
		$compra->ActualizarDatosCompra();
		echo $respuesta && $respuesta['resultado'] ?
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'El producto fue agregado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un erro al realizar la acción.']]);
		return;
	}
}