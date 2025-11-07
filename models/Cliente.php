<?php 

namespace Model;

Class Cliente extends ActiveRecord{	
	protected static $tabla = 'cliente';
	protected static $columnasDB = ['id_cliente', 'id_cliente_persona'];
	protected $id_cliente; 
	protected $id_cliente_persona;

	public function __construct($args = []){
		$this->idNombre = 'id_cliente';
		$this->id_cliente_persona = $args['id_cliente_persona'] ?? '';
	}
	public function getIdCliente() : int{
		return $this->id_cliente;
	}

	public function setIdCliente($id_cliente) : void{
		$this->id_cliente = $id_cliente;
	}

	public function getIdClientePersona() : int{
		return $this->id_cliente_persona;
	}

	public function setIdClientePersona($id_cliente_persona) : void{
		$this->id_cliente_persona = $id_cliente_persona;
	}
}