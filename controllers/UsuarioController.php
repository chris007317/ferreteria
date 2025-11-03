<?php 

namespace Controllers;

use mvc\Router;

use Model\Usuario;
use Model\TablaTabla;
use Model\Persona;

use Response\BandejaUsuarioResponse;

use Enum\Codigos;
use Enum\TipoDocumentoPersona;
use Enum\EstadoRegistro;

Class UsuarioController {
	public static function Index(Router $router){
		$respuesta['usuarios'] = Usuario::ListarUsuario(BandejaUsuarioResponse::class);
		$respuesta['tiposDocumentos'] = TablaTabla::whereAll('grupo', Codigos::TIPO_DOCUMENTO->value);
		$respuesta['tiposUsuarios'] = TablaTabla::whereAll('grupo', Codigos::TIPO_USUARIO->value);
		$router->render('/ajustes/usuarios/index',[
			'titulo' => 'Ajuste de usuarios',
			'script' => 'usuario',
			'datos' => $respuesta
		]);
	}

	public static function Crear(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$tipoDocumento = trim($_POST['cmbTipoDocumentoUsuario']);
		$tipoUsuario = trim($_POST['cmbTipoUsuario']);
		if(!TipoDocumentoPersona::esValido($tipoDocumento) || TipoDocumentoPersona::esValido($tipoUsuario)){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$tipoDocumentoRespuesta = TablaTabla::where('codigo', $tipoDocumento);
		$tipoUsuarioRespuesta = TablaTabla::where('codigo', $tipoUsuario);
		$estadoUsuario = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);

		if(!$tipoDocumentoRespuesta || !$tipoUsuarioRespuesta){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$args['id_tipo_doc'] = $tipoDocumentoRespuesta->getIdTabtab();
		$args['nombres'] = trim($_POST['txtNombresPersona']);
		$args['apellidos'] = trim($_POST['txtApellidos']);
		$args['num_documento'] = trim($_POST['txtDocumentoUsuario']);
		$args['telefono'] = trim($_POST['txtCelularPersona']);
		$args['username'] = trim($_POST['txtNombreUsuario']);
		$args['email'] = trim($_POST['txtCorreoPersona']);
		$args['password_hash'] = trim($_POST['txtContraUsuario']);
		$args['id_rol_usuario'] = $tipoUsuarioRespuesta->getIdTabtab();
		$args['id_estado_usuario'] = $estadoUsuario->getIdTabtab();

		$persona = Persona::where('num_documento', $args['num_documento']);
		if(!$persona){
			$persona = new Persona($args);
			$alertas = $persona->validar();
			if(!empty($alertas)){
				echo json_encode(['alerta'=>[$alertas][0]]);
				return;	
			}
			$personaRespuesta = $persona->guardar();
			if($personaRespuesta && $personaRespuesta['resultado']){
				$args['id_persona'] = $personaRespuesta['id'];
			}
		}else{
			$args['id_persona'] = $persona->getIdPersona();
		}
		$usuario = new Usuario($args);
		$alertas = $usuario->Validar();
		if(!empty($alertas)) {
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$usuarioExiste = $usuario->ExisteUsuario();
		if($usuarioExiste) {
	        echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'La persona ya se encuentra registrado como usuario en el sistema']
	        ]);
	        return;
		}
		$usuario->EncriptarContra();
		$respuesta = $usuario->guardar();
		if($respuesta && $respuesta['resultado']){
			echo json_encode(['alerta' => ['tipo' => 'ok','mensaje' => 'Usuario registrado con exito']]);
			return;
		}
		echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Ocurrio un error al realizar la acción']]);
		return;		
	}

	public static function Seleccionar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$idUsuario = $_GET['idUsuario'];
		if(!filter_var($idUsuario, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$usuario = Usuario::Seleccionar(BandejaUsuarioResponse::class, $idUsuario);
		if(!$usuario){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		echo json_encode([
			'alerta' => ['tipo' => 'ok'],
			'datos' => $usuario
		]);
		return;		
	}

	public static function Editar(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$tipoUsuario = trim($_POST['cmbTipoUsuario']);
		if(TipoDocumentoPersona::esValido($tipoUsuario)){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}
		$tipoUsuarioRespuesta = TablaTabla::where('codigo', $tipoUsuario);
		$estadoUsuario = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		if(!$tipoUsuarioRespuesta){
			echo json_encode([
	            'alerta' => ['tipo' => 'warning', 'mensaje' => 'Datos seleccionados invalidos']
	        ]);
	        return;
		}		
		$args['id_usuario'] = $_POST['idUsuario'];
		$args['telefono'] = trim($_POST['txtCelularPersona']);
		$args['email'] = trim($_POST['txtCorreoPersona']);
		$args['username'] = trim($_POST['txtNombreUsuario']);
		$args['id_rol_usuario'] = $tipoUsuarioRespuesta->getIdTabtab();
		if(!filter_var($args['id_usuario'], FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$usuarioExiste = new Usuario($args);
		if($usuarioExiste->ExisteNombreUsuario($estadoUsuario->getIdTabtab())){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Nombre de usuario ya se encuentra registrado']]);
			return;
		}
		$usuario = Usuario::whereArrayOne([
			'id_usuario'=> $args['id_usuario'],
			'id_estado_usuario' => $estadoUsuario->getIdTabtab()]);
		if(!$usuario){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$persona = Persona::where('id_persona', $usuario->getIdPersona());
		$persona->setTelefono($args['telefono']);
		if(!$persona->updateOnly()){
			echo json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar los datos del usuario']
	        ]);
	        return;
		}
		$usuario->sincronizar($args);
	    if (!$usuario->updateOnly()) {
	        echo json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar los datos del usuario']
	        ]);
	        return;
	    }
	    echo json_encode([
	        'alerta' => ['tipo' => 'ok', 'mensaje' => 'Usuario editado con exito']
	    ]);		
		return;
	}

	public static function ActualizarContra(){
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;
		$idUsuario = $_POST['idActualizar'];
		$nuevaContra = $_POST['txtNuevaContraUsuario'];
		if(!filter_var($idUsuario, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$estadoUsuario = TablaTabla::where('codigo', EstadoRegistro::ACTIVO->value);
		$usuario = Usuario::existeRegistro(['id_usuario'=> $idUsuario, 'id_estado_usuario' => $estadoUsuario->getIdTabtab()]);
		if (!$usuario) {
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$usuario->setPasswordHash($nuevaContra);
		$alertas = $usuario->ValidarContra();
		if(!empty($alertas)) {
			echo json_encode(['alerta'=>[$alertas][0]]);
			return;
		}
		$usuario->EncriptarContra();
		$actualizar = $usuario->updateOnly();
		if(!$actualizar){
			echo json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar la contraseña el usuario']
	        ]);
	        return;
		}
	    echo json_encode([
	        'alerta' => ['tipo' => 'ok', 'mensaje' => 'Contraseña actualizado con exito exito']
	    ]);	
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
		$estadoUsuario = TablaTabla::where('codigo', $estado);
		$idUsuario = $_POST['idAccion'];
		if(!filter_var($idUsuario, FILTER_VALIDATE_INT)){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Usuario no encontrado']]);
			return;
		}
		$usuario = Usuario::existeRegistro(['id_usuario' => $idUsuario]);
		if(!$usuario){
			echo json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'El usuario seleccionado no existe.']
	        ]);
		}
		$usuario->setIdEstadoUsuario($estadoUsuario->getIdTabtab());
		$actualizar = $usuario->updateOnly();
		if(!$actualizar){
			echo json_encode([
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo actualizar el estado del usuario']
	        ]);
	        return;
		}
	    echo json_encode([
	        'alerta' => ['tipo' => 'ok', 'mensaje' => 'Estado de usuario actualizado con exito']
	    ]);	
	    return;
	}
}