<?php 

namespace Model;

Class Proveedor extends ActiveRecord{
	protected static $tabla = 'proveedor';
	protected static $columnasDB = ['id_proveedor', 'id_estado_proveedor', 'razon_social', 'num_documento', 'direccion', 'telefono', 'email', 'eliminado'];	
	protected $id_proveedor;
	protected $id_estado_proveedor;
	protected $razon_social;
	protected $num_documento;
	protected $direccion;
	protected $telefono;
	protected $email;
	protected $eliminado;

	public function __construct($args = []){
		$this->idNombre = 'id_proveedor';
		$this->id_proveedor = $args['id_proveedor'] ?? null;
		$this->razon_social = $args['razon_social'] ?? null;
        $this->num_documento = $args['num_documento'] ?? null;
		$this->direccion = $args['direccion'] ?? null;
		$this->email = $args['email'] ?? null;
		$this->telefono = $args['telefono'] ?? null;
		$this->id_estado_proveedor = $args['id_estado_proveedor'] ?? null;
	}

	public function getIdProveedor() : int{
		return $this->id_proveedor;
	}

	public function setIdProveedor($id_proveedor) : void{
		$this->id_proveedor = $id_proveedor;
	}

	public function getIdEstadoProveedor() : int{
		return $this->id_estado_proveedor;
	}

	public function setIdEstadoProveedor($id_estado_proveedor) : void{
		$this->id_estado_proveedor = $id_estado_proveedor;
	}

	public function getRazonSocial() : string{
		return $this->razon_social;
	}

	public function setRazonSocial($razon_social) : void{
		$this->razon_social = $razon_social;
	}

	public function getNumDocumento() : string{
		return $this->num_documento;
	}

	public function setNumDocumento($num_documento) : void{
		$this->num_documento = $num_documento;
	}

	public function getDireccion() : string{
		return $this->direccion;
	}

	public function setDireccion($direccion) : void{
		$this->direccion = $direccion;
	}

	public function getTelefono() : string{
		return $this->telefono;
	}

	public function setTelefono($telefono) : void{
		$this->telefono = $telefono;
	}

	public function getEmail() : string{
		return $this->email;
	}

	public function setEmail($email) : void{
		$this->email = $email;
	}

	public function getEliminado() : bool{
		return $this->eliminado;
	}

	public function setEliminado($eliminado) : void{
		$this->eliminado = $eliminado;
	}

	public function Validar() {
	    if (empty($this->num_documento) || empty($this->razon_social) || empty($this->direccion)) {
	        self::setAlerta('error', 'El RUC, razón social y la dirección son obligatorios.');
	        return self::$alertas;
	    }
	    if (
	        !preg_match('/^[0-9]+$/', $this->num_documento) || 
	        !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\-\-#\/()°]{3,255}$/u', $this->direccion) ||
	        !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 &.,\-\/()°]{3,150}$/u', $this->razon_social)
	    ) {
	        self::setAlerta('error', 'No ingrese caracteres especiales.');
	        return self::$alertas;
	    }
	    if (strlen($this->num_documento) != 11) {
	        self::setAlerta('error', 'El RUC debe tener 11 caracteres.');
	        return self::$alertas;
	    }
	    if (!empty($this->telefono) && 
	        (!preg_match('/^[0-9]+$/', $this->telefono) || strlen($this->telefono) != 9)
	    ) {
	        self::setAlerta('error', 'Celular ingresado inválido.');
	        return self::$alertas;
	    }
	    if (!empty($this->email) && 
	        !preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $this->email)
	    ) {
	        self::setAlerta('error', 'Correo ingresado inválido.');
	        return self::$alertas;
	    }
	    return self::$alertas;
	}

	public function ExisteRucProveedor() : bool{
        $query = "SELECT id_proveedor FROM proveedor
            WHERE 
                id_proveedor != $this->id_proveedor AND
                num_documento = '$this->num_documento'
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        return $resultado ? true : false;
	}
}