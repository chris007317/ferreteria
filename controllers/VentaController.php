<?php 

namespace Controllers;

use MVC\Router;

Class VentaController {
	public static function Index(Router $router){
		$router->render('/ventas/index',[
			'titulo' => 'Nueva venta',
			'script' => 'ventas',
		]);
	}
}