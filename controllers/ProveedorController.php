<?php 

namespace Controllers;

use MVC\Router;

use Model\Proveedor;
use Model\TablaTabla;

use Response\BandejaProveedorResponse;

use Enum\EstadoRegistro;

Class ProveedorController{

	public static function Index(Router $router){
		$respuesta['proveedores'] = Proveedor::joinToModel(
			BandejaProveedorResponse::class,  
		    ['tabtab'],
		    ['id_estado_proveedor'], 
		    ['id_tabtab'],
		    [
		    	'eliminado' => FALSE
		    ],
		    'id_proveedor' ,'DESC'
		);
		$router->render('/proveedor/index',[
			'titulo' => 'Proveedores',
			'script' => 'proveedor',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$args['num_documento'] = $_POST['txtDocumentoProveedor'];
		$args['razon_social'] = $_POST['txtRazonSocialProveedor'];
		$args['direccion'] = $_POST['txtDireccionProveedor'];
		$args['email'] = $_POST['txtCorreoProveedor'];
		$args['telefono'] = $_POST['txtCelular'];
		$proveedor = new Proveedor($args);
		$alertas = $proveedor->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$existeProveedor = Proveedor::existeRegistro([
			'num_documento' => $args['num_documento'],
			'eliminado' => FALSE
		]);
		if ($existeProveedor) {
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor ya se encuentra registrado']]);
			return;
		}
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$proveedor->setIdEstadoProveedor($estadoRegistro->getIdTabtab());
		$respuesta = $proveedor->guardar();
		echo $respuesta && $respuesta['resultado'] ?
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Proveedor registrado con exito']]) :
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un erro al realizar la acción.']]);
		return;
	}

	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idProveedor = trim($_GET['idProveedor']);
		if(!filter_var($idProveedor, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Proveedor no encontrado']]);
			return;
		}
		$proveedor = Proveedor::where('id_proveedor', $idProveedor);
		echo !$proveedor ?
			json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado no fue encontrado']]) :
			json_encode(['alerta' => ['tipo' => 'ok'],'datos' => $proveedor->toArray()]);
		return;
	}

	public static function Editar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idProveedor = $_POST['idProveedor'];
		$args['num_documento'] = $_POST['txtDocumentoProveedor'];
		$args['razon_social'] = $_POST['txtRazonSocialProveedor'];
		$args['direccion'] = $_POST['txtDireccionProveedor'];
		$args['email'] = $_POST['txtCorreoProveedor'];
		$args['telefono'] = $_POST['txtCelular'];
		if(!filter_var($idProveedor, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Proveedor no encontrado']]);
			return;
		}
		$estadoRegistro = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$proveedor = Proveedor::existeRegistro([
			'id_proveedor' => $idProveedor,
			'id_estado_proveedor' => $estadoRegistro->getIdTabtab(),
			'eliminado' => FALSE
		]);
		if(!$proveedor){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado no fue encontrado']]);
			return;
		}
		$proveedor->sincronizar($args);
		if($proveedor->ExisteRucProveedor()){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El RUC ya se encuentra registrado en otro proveedor']]);
			return;
		}
		$alertas = $proveedor->Validar();
		if(!empty($alertas)){
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$respuesta = $proveedor->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Proveedor actualizado con exito']]) :
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
		$idProveedor = $_POST['idAccion'];
		if(!filter_var($idProveedor, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Proveedor no encontrado']]);
			return;
		}
		$proveedor = Proveedor::existeRegistro([
			'id_proveedor' => $idProveedor,
			'eliminado' => FALSE
		]);
		if(!$proveedor){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'El proveedor seleccionado no fue encontrado']]);
		}
		$proveedor->setIdEstadoProveedor($estadoRegistro->getIdTabtab());
		$respuesta = $proveedor->updateOnly();
		echo $respuesta ? 
			json_encode(['alerta' => ['tipo' => 'ok', 'mensaje' => 'Estado del proveedor actualizado con exito']]) :
			json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el estado del usuario']
	        ]);
        return;
	}	
}