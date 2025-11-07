<?php 

namespace Controllers;

use MVC\Router;

use Model\Producto;
use Model\TablaTabla;

use Response\ListaProductosResponse;

use Enum\EstadoRegistro;
use Enum\EstadoProducto;
use Enum\Codigos;

Class VentaController {
	public static function Index(Router $router){
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$estadoProducto = TablaTabla::where('codigo', EstadoProducto::EN_ALMACEN->value);
		$respuesta['tiposDocumentos'] = TablaTabla::whereAll('grupo', Codigos::TIPO_DOCUMENTO->value);
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
}