<?php

namespace Controllers;

use MVC\Router;


class InicioController {
	public static function index(Router $router){
		$router->render('/inicio/index',[
			'titulo' => 'Inicio',
			'script' => 'inicio'
		]);		
	}

	public static function CerrarSesion(Router $router){
		$_SESSION = [];
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;	
	}
}