<?php 

namespace Response;

use Enum\EstadoRegistro;

Class BandejaProveedorResponse{

	public $id_proveedor;
	public $razon_social;
	public $num_documento;
	public $direccion;
	public $telefono;
	public $email;
	public $nombre;
	public $codigo;

	public function TextoAccion() : string{
        return $this->codigo == EstadoRegistro::ACTIVO->value ? 'Desactivar proveedor' : 'Activar provedor';
    }
}