<?php 

namespace Controllers;

use MVC\Router;

use Model\Compra;
use Model\Proveedor;
use Model\TablaTabla;
use Model\Categoria;

use Enum\EstadoRegistro;

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
		$respuesta['proveedor'] = Proveedor::where('id_proveedor', $respuesta['compra']->getIdProveedor());
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$respuesta['proveedores'] = Proveedor::whereAll('id_estado_proveedor', $estadoRegistro->getIdTabtab());
		$respuesta['categorias'] = Categoria::whereAll('id_estado_categoria', $estadoRegistro->getIdTabtab());
		$router->render('/compras/productos',[
			'titulo' => 'Productos',
			'script' => 'compras-productos',
			'datos' => $respuesta
		]);
	}
}