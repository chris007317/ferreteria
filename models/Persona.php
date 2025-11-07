<?php 

namespace Model;

Class Persona extends ActiveRecord{
	protected static $tabla = 'personas';
	protected static $columnasDB = ['id_persona', 'id_tipo_doc', 'nombres', 'apellidos', 'num_documento', 'telefono'];
	protected $id_persona;
	protected $id_tipo_doc;
	protected $nombres;
	protected $apellidos;
	protected $num_documento;
	protected $telefono;

	public function __construct($args = []){
		$this->idNombre = 'id_persona';
		$this->id_tipo_doc = $args['id_tipo_doc'] ?? '';
		$this->nombres = $args['nombres'] ?? '';
		$this->apellidos = $args['apellidos'] ?? '';
		$this->num_documento = $args['num_documento'] ?? '';
		$this->telefono = $args['telefono'] ?? '';
	}

	public function getIdPersona() : int{
		return $this->id_persona;
	}

	public function setIdPersona($id_persona) : void{
		$this->id_persona = $id_persona;
	}

	public function getIdTipoDoc() : int{
		return $this->id_tipo_doc;
	}

	public function setIdTipoDoc($id_tipo_doc) : void{
		$this->id_tipo_doc = $id_tipo_doc;
	}

	public function getNombres() : string{
		return $this->nombres;
	}

	public function setNombres($nombres) : void{
		$this->nombres = $nombres;
	}

	public function getApellidos() : string{
		return $this->apellidos;
	}

	public function setApellidos($apellidos) : void{
		$this->apellidos = $apellidos;
	}

	public function getNumDocumento() : string{
		return $this->num_documento;
	}

	public function setNumDocumento($num_documento) : void{
		$this->num_documento = $num_documento;
	}

	public function getTelefono() : string{
		return $this->telefono;
	}

	public function setTelefono($telefono) : void{
		$this->telefono = $telefono;
	}

    public function validar(){
	    $regexTexto = '/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/';

	    if (empty($this->nombres) || !preg_match($regexTexto, $this->nombres) ||
	        empty($this->apellidos) || !preg_match($regexTexto, $this->apellidos)) {
	        self::setAlerta('warning', 'Registre nombres y apellidos válidos (solo letras)');
	        return self::$alertas;
	    }

	    if(strlen($this->nombres) > 60){
	    	self::setAlerta('warning', 'El nombre debe tener como maximo 60 caracteres');
	        return self::$alertas;
	    }

	    if(strlen($this->apellidos) > 150){
	    	self::setAlerta('warning', 'Los apellidos deben tener como maximo 150 caracteres');
	        return self::$alertas;
	    }
	    return self::$alertas;
    }

    public function validarDniPersona(){
    	$this->validar();
    	if (!isset($this->num_documento) || empty($this->num_documento) || !preg_match('/^[0-9]{8}$/', $this->num_documento)){
            self::setAlerta('warning', 'DNI ingresado no es valido');
            return self::$alertas;
        }
    }
}