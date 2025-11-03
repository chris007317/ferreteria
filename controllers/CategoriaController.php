<?php 

namespace Controllers;

use mvc\Router;

use Model\Categoria;
use Model\TablaTabla;

use Response\BandejaCategoriaResponse;
use Response\PaginadorResponse;

use Request\BuscarCategoriaRequest;

use Enum\EstadoRegistro;

Class CategoriaController {

	public static function Index(Router $router){
		$filtros = new BuscarCategoriaRequest($_GET ?? []);
		$respuesta['filtros'] = $filtros;
		$paginaActual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
		$paginaActual = max(1, $paginaActual);
    	$porPagina = 10;
		$respuesta['categorias'] = Categoria::ListarCategorias(
			BandejaCategoriaResponse::class,
			$paginaActual,
			$porPagina,
			$filtros
		);
		$totalRegistros = Categoria::BuscarTotalCategorias($filtros);
		$totalPaginas =ceil($totalRegistros / $porPagina);
		$respuesta['paginador'] = new PaginadorResponse($paginaActual, $porPagina, $totalRegistros);
		$router->render('/ajustes/categoria/index',[
			'titulo' => 'Ajuste de ctegoria',
			'script' => 'categoria',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$args['nombre'] = $_POST['txtNombreCategoria'];
		$args['descripcion'] = $_POST['txtDescripcionCategoria'];
		$categoria = new Categoria($args);
		$alerta = $categoria->validar();
		if(!empty($alerta)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$existeCategoria = Categoria::existeRegistro(['nombre' => $args['nombre']]);
		if($existeCategoria){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El nombre de la categroría ya se encuentra registrado']]);
			return;
		}
		$estadoCategoria = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$categoria->setIdEstadoCategoria($estadoCategoria->getIdTabtab());
		$respuesta = $categoria->guardar();
			if($respuesta && $respuesta['resultado']){
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Categoría registrado con exito']]);
			return;
		}
		echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un error al realizar la acción']]);
		return;
	}

	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idCategoria = $_GET['idCategoria'];
		if(!filter_var($idCategoria, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Categoría no encontrado']]);
			return;
		}
		$categoria = Categoria::where('id_categoria', $idCategoria);
		if(!$categoria){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La categoría seleccionada no fue encontrado']]);
			return;
		}
		echo json_encode([
			'alerta' => ['tipo' => 'ok'],
			'datos' => $categoria->toArray()
		]);
		return;
	}

	public static function Editar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idCategoria = $_POST['idCategoria'];
		$args['nombre'] = $_POST['txtNombreCategoria'];
		$args['descripcion'] = $_POST['txtDescripcionCategoria'];
		if(!filter_var($idCategoria, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Categoría no encontrado']]);
			return;
		}
		$categoria = Categoria::existeRegistro(['id_categoria' => $idCategoria]);
		if(!$categoria){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La categoría seleccionada no fue encontrado']]);
		}
		$categoria->sincronizar($args);
		$alerta = $categoria->validar();
		if(!empty($alerta)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$respuesta = $categoria->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Categoría actualizado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un error al realizar la acción']]);
		return;
	}

	public static function CambiarEstado()	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$estado = trim($_POST['estado']);
		if(!EstadoRegistro::esValido($estado)){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$estadoCategoria = TablaTabla::where('codigo', $estado);
		$idCategoria = $_POST['idAccion'];
		if(!filter_var($idCategoria, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Categoría no encontrado']]);
			return;
		}
		$categoria = Categoria::existeRegistro(['id_categoria' => $idCategoria]);
		if(!$categoria){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'La categoría seleccionada no fue encontrado']]);
		}
		$categoria->setIdEstadoCategoria($estadoCategoria->getIdTabtab());
		$respuesta = $categoria->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok', 'mensaje' => 'Estado de la categoria actualizado con exito']]) :
			json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el estado del usuario']
	        ]);
        return;
	}
}