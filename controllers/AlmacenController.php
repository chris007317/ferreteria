<?php 

namespace Controllers;

use MVC\Router;

use Model\Almacen;
use Model\TablaTabla;

use Response\BandejaAlmacenResponse;

use Enum\EstadoRegistro;

Class AlmacenController{

	public static function Index(Router $router){
		$respuesta['almacenes'] = Almacen::joinToModel(
			BandejaAlmacenResponse::class,  
		    ['tabtab'],
		    ['id_estado_almacen'], 
		    ['id_tabtab'],
		    [],
		    'id_almacen' ,'DESC'
		);
		$router->render('/almacen/index',[
			'titulo' => 'Almacenes',
			'script' => 'almacen',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$args['nombre_almacen'] = $_POST['txtNombreAlmacen'];
		$args['direccion'] = $_POST['txtDireccionAlmacen'];
		$almacen = new Almacen($args);
		$alertas = $almacen->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$existeRegistro = Almacen::existeRegistro([
			'nombre_almacen' => $args['nombre_almacen']
		]);
		if ($existeRegistro) {
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El nombre del almacén ya se encuentra registrado']]);
			return;
		}
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$almacen->setIdEstadoAlmacen($estadoRegistro->getIdTabtab());
		$respuesta = $almacen->guardar();
		echo $respuesta && $respuesta['resultado'] ?
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Almacén registrado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un erro al realizar la acción.']]);
		return;
	}

	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idAlmacen = trim($_GET['idAlmacen']);
		if(!filter_var($idAlmacen, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Almacén no encontrado']]);
			return;
		}
		$almacen = Almacen::where('id_almacen', $idAlmacen);
		echo !$almacen ?
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El almacén seleccionado no fue encontrado']]) :
			json_encode(['alerta' => ['tipo' => 'ok'],'datos' => $almacen->toArray()]);
		return;
	}

	public static function Editar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idAlmacen = $_POST['idAlmacen'];
		$args['nombre_almacen'] = $_POST['txtNombreAlmacen'];
		$args['direccion'] = $_POST['txtDireccionAlmacen'];
		if(!filter_var($idAlmacen, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Almacén no encontrado']]);
			return;
		}
		$almacen = Almacen::existeRegistro([
			'id_almacen' => $idAlmacen
		]);
		if(!$almacen){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El almacén seleccionado no fue encontrado']]);
			return;
		}
		$almacen->sincronizar($args);
		$alertas = $almacen->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		if($almacen->ExisteNombreAlmacen()){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El nombre de almacén ya se encuentra registrado']]);
			return;
		}
		$respuesta = $almacen->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Almacén actualizado con exito']]) :
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
		$estadoRegistro = TablaTabla::where('codigo', $estado);
		$idAlmacen = $_POST['idAccion'];
		if(!filter_var($idAlmacen, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Almacén no encontrado']]);
			return;
		}
		$almacen = Almacen::existeRegistro([
			'id_almacen' => $idAlmacen
		]);
		if(!$almacen){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El almacén seleccionado no fue encontrado']]);
		}
		$almacen->setIdEstadoAlmacen($estadoRegistro->getIdTabtab());
		$respuesta = $almacen->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok', 'mensaje' => 'Estado del almacén actualizado con exito']]) :
			json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el estado del usuario']
	        ]);
        return;
	}
}