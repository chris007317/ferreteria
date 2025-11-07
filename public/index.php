<?php 

require __DIR__ . '/../includes/app.php';

use MVC\Router;

use Controllers\LoginController;
use Controllers\InicioController;
use Controllers\UsuarioController;
use Controllers\PersonaController;
use Controllers\CategoriaController;
use Controllers\ProveedorController;
use Controllers\AlmacenController;
use Controllers\CompraController;
use Controllers\CompraProductoController;
use Controllers\VentaController;
use Controllers\ProductoController;

$router = new Router();

$router->get('/', [InicioController::class, 'index'], true);
$router->get('/inicio', [InicioController::class, 'index'], true);
$router->get('/cerrar-sesion', [InicioController::class, 'CerrarSesion'], true);

//Rutas para el login
$router->get('/login', [LoginController::class, 'login'], false);
$router->post('/login', [LoginController::class, 'IniciarSesion'], false);

$router->get('/ajustes/usuarios', [UsuarioController::class, 'Index'], true);
$router->get('/ajustes/usuarios/seleccionar', [UsuarioController::class, 'Seleccionar'], true);
$router->post('/ajustes/usuarios/crear', [UsuarioController::class, 'Crear'], true);
$router->post('/ajustes/usuarios/editar', [UsuarioController::class, 'Editar'], true);
$router->post('/ajustes/usuarios/nueva-contra', [UsuarioController::class, 'ActualizarContra'], true);
$router->post('/ajustes/usuarios/cambiar-estado', [UsuarioController::class, 'CambiarEstado'], true);

$router->get('/ajustes/categoria', [CategoriaController::class, 'Index'], true);
$router->get('/ajustes/categoria/seleccionar', [CategoriaController::class, 'Seleccionar'], true);
$router->post('/ajustes/categoria/crear', [CategoriaController::class, 'Crear'], true);
$router->post('/ajustes/categoria/editar', [CategoriaController::class, 'Editar'], true);
$router->post('/ajustes/categoria/cambiar-estado', [CategoriaController::class, 'CambiarEstado'], true);

$router->get('/ajustes/personas/buscarDocumento', [PersonaController::class, 'BuscarPorDocumento'], true);
$router->get('/ajustes/personas/buscarRuc', [PersonaController::class, 'buscarRuc'], true);

$router->get('/proveedores', [ProveedorController::class, 'Index'], true);
$router->get('/proveedores/seleccionar', [ProveedorController::class, 'Seleccionar'], true);
$router->post('/proveedores/crear', [ProveedorController::class, 'Crear'], true);
$router->post('/proveedores/editar', [ProveedorController::class, 'Editar'], true);
$router->post('/proveedores/cambiar-estado', [ProveedorController::class, 'CambiarEstado'], true);

$router->get('/almacenes', [AlmacenController::class, 'Index'], true);
$router->get('/almacenes/seleccionar', [AlmacenController::class, 'Seleccionar'], true);
$router->post('/almacenes/crear', [AlmacenController::class, 'Crear'], true);
$router->post('/almacenes/editar', [AlmacenController::class, 'Editar'], true);
$router->post('/almacenes/cambiar-estado', [AlmacenController::class, 'CambiarEstado'], true);

$router->get('/compras', [CompraController::class, 'Index'], true);
$router->get('/compras/seleccionar', [CompraController::class, 'Seleccionar'], true);
$router->post('/compras/crear', [CompraController::class, 'Crear'], true);
$router->post('/compras/editar', [CompraController::class, 'Editar'], true);
$router->post('/compras/eliminar', [CompraController::class, 'Eliminar'], true);

$router->get('/compras/productos', [CompraProductoController::class, 'Index'], true);
$router->get('/compras/productos/por-almacen-categoria', [CompraProductoController::class, 'SeleccionarPorCategoriaAlmacen'], true);
$router->post('/compras/productos/crear', [CompraProductoController::class, 'Crear'], true);
$router->post('/compras/productos/aprobar', [CompraProductoController::class, 'AprobarCompra'], true);
$router->post('/compras/productos/recibir', [CompraProductoController::class, 'RecibirCompra'], true);
$router->post('/compras/productos/agregar', [CompraProductoController::class, 'AgregarProducto'], true);

$router->get('/ventas', [VentaController::class, 'Index'], true);

$router->get('/productos/seleccionar', [ProductoController::class, 'Seleccionar'], true);

$router->comprobarRutas();