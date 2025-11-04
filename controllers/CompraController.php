<?php 

namespace Controllers;

use MVC\Router;

use Model\Proveedor;
use Model\TablaTabla;
use Model\Compra;

use Response\BandejaCompraResponse;
use Response\PaginadorResponse;

use Request\BuscarCompraRequest;

use Enum\EstadoRegistro;
use Enum\EstadoCompra;

Class CompraController{
	public static function Index(Router $router){
		$filtros = new BuscarCompraRequest($_GET ?? []);
		$respuesta['filtros'] = $filtros;
		$paginaActual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
		$paginaActual = max(1, $paginaActual);
    	$porPagina = 10;
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value, 'nombre', 'ASC');
		$respuesta['compras'] = Compra::BuscarCompras(
			BandejaCompraResponse::class,
			$paginaActual,
			$porPagina,
			$filtros
		);
		$totalRegistros = Compra::BuscarTotalCompra($filtros);
		$totalPaginas =ceil($totalRegistros / $porPagina);
		$respuesta['paginador'] = new PaginadorResponse($paginaActual, $porPagina, $totalRegistros);
		$respuesta['proveedores'] = Proveedor::whereAll('id_estado_proveedor', $estadoRegistro->getIdTabtab());
		$router->render('/compras/index',[
			'titulo' => 'Compras',
			'script' => 'compras',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$args['id_proveedor'] = trim($_POST['cmbProveedor']);
		$args['numero_compra'] = trim($_POST['txtNumeroCompra']);
		$args['fecha'] = trim($_POST['txtFechaCompra']);
		$args['total'] = floatval($_POST['txtTotalCompra']);
		$args['igv'] = floatval($_POST['txtIgvCompra']);
		$args['observaciones'] = trim($_POST['txtObservacionesCompra']);
		if(!filter_var($args['id_proveedor'], FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado es invalido']]);
			return;
		}
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$existeProveedor = Proveedor::existeRegistro([
			'id_proveedor' => $args['id_proveedor'],
			'id_estado_proveedor' => $estadoRegistro->getIdTabtab()
		]);
		if(!$existeProveedor){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'No existe proveedor seleccionado']]);
			return;
		}
		$existeCompra = Compra::existeRegistro([
			'numero_compra' => $args['numero_compra']
		]);
		if($existeCompra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El número de compra ya existe']]);
			return;
		}
		$estadoCompra = TablaTabla::where('codigo', EstadoCompra::PENDIENTE->value);
		$args['id_estado_compra'] = $estadoCompra->getIdTabtab();
		$compra = new Compra($args);
		$alertas = $compra->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$compra->CalcularSubTotal();
		$respuesta = $compra->guardar();
		echo $respuesta && $respuesta['resultado'] ?
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Compra registrado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un erro al realizar la acción.']]);
		return;
	}

	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idCompra = trim($_GET['idCompra']);
		if(!filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Compra no encontrado']]);
			return;
		}
		$compra = Compra::where('id_compra', $idCompra);
		echo !$compra ?
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La compra seleccionado no fue encontrado']]) :
			json_encode(['alerta' => ['tipo' => 'ok'],'datos' => $compra->toArray()]);
		return;
	}

	public static function Editar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idCompra = $_POST['idCompra'];
		$args['id_proveedor'] = trim($_POST['cmbProveedor']);
		$args['numero_compra'] = trim($_POST['txtNumeroCompra']);
		$args['fecha'] = trim($_POST['txtFechaCompra']);
		$args['total'] = floatval($_POST['txtTotalCompra']);
		$args['igv'] = floatval($_POST['txtIgvCompra']);
		$args['observaciones'] = trim($_POST['txtObservacionesCompra']);
		if(!filter_var($args['id_proveedor'], FILTER_VALIDATE_INT) || !filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado es invalido']]);
			return;
		}
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$existeProveedor = Proveedor::existeRegistro([
			'id_proveedor' => $args['id_proveedor'],
			'id_estado_proveedor' => $estadoRegistro->getIdTabtab()
		]);
		if(!$existeProveedor){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado no fue encontrado']]);
			return;
		}
		$estadoCompra = TablaTabla::where('codigo', EstadoCompra::PENDIENTE->value);
		$compra = Compra::existeRegistro([
			'id_compra' => $idCompra,
			'id_estado_compra' => $estadoCompra->getIdTabtab(),
			'compra_eliminado' => FALSE
		]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La compra seleccionado no fue encontrado']]);
			return;
		}
		$compra->sincronizar($args);
		$alertas = $compra->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		if($compra->ExisteNumeroCompra()){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El número de compra ya se encuentra registrado en otro proveedor']]);
			return;
		}
		$respuesta = $compra->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Compra actualizado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un error al realizar la acción']]);
		return;
	}

	public static function Eliminar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idCompra = $_POST['idEliminar'];
		if(!filter_var($idCompra, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado es invalido']]);
			return;
		}
		$compra = Compra::existeRegistro(['id_compra' => $idCompra, 'compra_eliminado' => FALSE]);
		if(!$compra){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Compra no encontrado']]);
			return;			
		}
		$compra->setCompraEliminado(TRUE);
		$respuesta = $compra->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok', 'mensaje' => 'La compra fue eliminado con exito']]) :
			json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el estado del usuario']
	        ]);
        return;
	}
}