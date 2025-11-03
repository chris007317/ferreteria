<?php 

namespace Controllers;

use Model\Persona;
use Model\TablaTabla;

use Enum\TipoDocumentoPersona;

Class PersonaController {

	public static function BuscarPorDocumento(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		if(!isset($_GET['tipo']) || empty($_GET['tipo']) || !isset($_GET['documento'])){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'Datos enviados incorrectamente']]);
			return;
		}
		$documento = $_GET['documento'];
	    $persona = Persona::whereArrayOne(['num_documento' => $documento]);
	    if ($persona) {
	        //$persona->razon_social = $persona->ObtenerDatosPersona();
	        echo json_encode( [
	            'alerta' => ['tipo' => 'ok'],
	            'datos' => $persona->toArray()
	        ]);
	        return;
	    }
	    $tipo = $_GET['tipo'];
	    if($tipo != TipoDocumentoPersona::DNI->value){
			echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'No se encontraron los datos de la persona']]);
			return;
	    }
    	$personaRespuesta = buscarPersonaDni($documento);
	    if (!isset($personaRespuesta['tipo']) || $personaRespuesta['tipo'] !== 'ok') {
	        echo json_encode( [
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'Error al consultar el servicio externo']
	        ]);
	        return;
	    }
	    if (!isset($personaRespuesta['dato']['first_name'])) {
	        echo json_encode( [
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'Persona no encontrada']
	        ]);
	        return;
	    }
	    $tipoDocumento = TablaTabla::where('codigo', $tipo);
	    if(!$tipoDocumento){
	    	echo json_encode( [
	            'alerta' => ['tipo' => 'error', 'mensaje' => 'Persona no encontrada']
	        ]);
	        return;
	    }
	    $args = [
	    	'id_tipo_doc' => $tipoDocumento->getIdTabtab(),
	        'num_documento' => $personaRespuesta['dato']['document_number'],
	        'nombres'  => $personaRespuesta['dato']['first_name'],
	        'apellidos' => $personaRespuesta['dato']['first_last_name'] . ' ' . $personaRespuesta['dato']['second_last_name']
	    ];
	    $personaNueva = new Persona($args);
	    $alertas = $personaNueva->validarDniPersona();
	    if (!empty($alertas)) {
	        return ['alerta' => $alertas[0]];
	    }
		$respuesta = $personaNueva->guardar();
	    if ($respuesta && $respuesta['resultado']) {
	        $personaNueva->setIdPersona($respuesta['id']);
	        //$personaNueva->razon_social = $personaNueva->ObtenerDatosPersona();
	        echo json_encode( [
	            'alerta' => ['tipo' => 'ok'],
	            'datos' => $personaNueva->toArray()
	        ]);
	        return;
	    }
	    echo json_encode( [
	        'alerta' => ['tipo' => 'error', 'mensaje' => 'No se pudo guardar la persona']
	    ]);
	    return;
	}

	public static function buscarRuc(){
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') return;
		$numeroDocumento = trim($_GET['documento']);
		if (!empty($numeroDocumento) && preg_match('/^[0-9]+$/', $numeroDocumento) && strlen($numeroDocumento) == 11) {
			$rucRespuesta = buscarRucEmpresa($numeroDocumento);
			if(isset($rucRespuesta['tipo']) && $rucRespuesta['tipo'] == 'ok'){
				if (!isset($rucRespuesta['dato']['razon_social'])) {
					echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'RUC no encontrado']]);
					return;			
				}
				echo json_encode([
					'alerta' => ['tipo' => 'ok'],
					'datos' => $rucRespuesta['dato']
				]);
				return;
			}
		}
		echo json_encode(['alerta' => ['tipo' => 'error','mensaje' => 'RUC no encontrado']]);
		return;
	}
}
