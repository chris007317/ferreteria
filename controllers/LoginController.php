<?php

namespace Controllers;

use MVC\Router;

use Model\Usuario;
use Model\Persona;

class LoginController {
	public static function login(Router $router){
		if(isset($_SESSION['usuario_id'])) header('Location: /inicio');  
		$router->render(
			'/login/index',
			[
				'titulo' => 'Iniciar Sesión',
				'script' => 'login'
			],
			'externo-layout'
		);		
	}

	public static function IniciarSesion(Router $router){
		if($_SERVER['REQUEST_METHOD'] === 'POST') {
			$args['username'] = filter_input(INPUT_POST, 'txtUsuarioLogin', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $usuario = new Usuario($args);
			$alertas = $usuario->ValidarUsuario($_POST['txtContraLogin']);
			if(!empty($alertas)){
				$router->render(
				'/login/index',
				[
					'titulo' => 'Iniciar Sesión',
					'script' => 'login',
					'alertas' => $alertas
				],
				'externo-layout'
				);	
				return;
			}
			$persona = Persona::where('id_persona', $usuario->getIdPersona());
			$_SESSION['persona_datos'] = $persona->getNombres() . ' ' . $persona->getApellidos();
			$_SESSION['usuario_id'] = $usuario->getIdUsuario();
			$_SESSION['ultima_actividad'] = time();
			header('Location: /inicio');  
		}
	}
}