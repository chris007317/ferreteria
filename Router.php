<?php

namespace MVC;

use Controllers\HelperController;

class Router
{
    public array $routes = [];

    public function __construct()
    {
        // Iniciar la sesión al crear el router
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function addRoute($method, $url, $fn, $requireAuth = true)
    {
        $method = strtoupper($method);
        $this->routes[$method][$url] = [
            'callback' => $fn,
            'auth' => $requireAuth
        ];
    }

    public function get($url, $fn, $requireAuth = true)
    {
        $this->addRoute('GET', $url, $fn, $requireAuth);
    }

    public function post($url, $fn, $requireAuth = true)
    {
        $this->addRoute('POST', $url, $fn, $requireAuth);
    }

    public function comprobarRutas()
    {
        $url_actual = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        $route = $this->routes[$method][$url_actual] ?? null;
        if ($route) {
            if ($route['auth'] && !$this->validarSesion()) {
                header('Location: /login');
                exit;
            }
            call_user_func($route['callback'], $this);
        } else {
            http_response_code(404);
            echo 'Página no encontrada';
        }
    }

    private function validarSesion(): bool
    {
        if (!isset($_SESSION['usuario_id'], $_SESSION['ultima_actividad'])) 
            return false;
        // Validar tiempo de inactividad
        $tiempo_actual = time();
        $tiempo_expiracion = $_ENV['TIEMPO_ACTIVIDAD']; // 15 minutos
        if ($tiempo_actual - $_SESSION['ultima_actividad'] > $tiempo_expiracion) {
            session_unset();
            session_destroy();
            return false;
        }
        // Actualizar tiempo de actividad
        $_SESSION['ultima_actividad'] = $tiempo_actual;
        return true;
    }

    public function render($view, $variables = [], $layout = 'layout')
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }        
        $variables['rolUsuario'] = $_SESSION['usuario_tipo'] ?? null;
        foreach ($variables as $key => $value) {
            $$key = $value;
        }

        ob_start();
        $viewPath = __DIR__ . "/views/$view.php";

        if (file_exists($viewPath)) {
            include_once $viewPath;
        } else {
            throw new \Exception("Vista $view no encontrada");
        }

        $contenido = ob_get_clean();

        $layoutPath = __DIR__ . "/views/$layout.php";

        if (file_exists($layoutPath)) {
            include_once $layoutPath;
        } else {
            throw new \Exception("Layout $layout no encontrado");
        }
    }
}