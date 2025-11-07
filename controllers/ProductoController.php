<?php 

namespace Controllers;

use Model\Producto;
use Model\TablaTabla;

use Enum\EstadoProducto;

Class ProductoController {
	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idProducto = trim($_GET['idProducto']);
		if(!filter_var($idProducto, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Producto no encontrado']]);
			return;
		}
		$estadoProducto = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$producto = Producto::whereArrayOne([
			'id_producto' => $idProducto,
			'id_estado_producto' => $estadoProducto->getIdTabtab(),
			'producto_eliminado' => FALSE
		]);
		if(!$producto){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El producto seleccionado no fue encontrado']]);
			return;
		}
		echo $producto->getStock() == 0 ?
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El producto seleccionado no cuenta con estock en el almacén']]) :
			json_encode(['alerta' => ['tipo' => 'ok'],'datos' => $producto->toArray()]);
	}
}