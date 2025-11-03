<?php 

namespace Response;

use Enum\TipoUsuario;
use Enum\EstadoRegistro;

Class BandejaUsuarioResponse{
	public $id_usuario; 
    public $id_persona;
    public $username;
    public $email;
    public $nombres;
    public $apellidos;
    public $telefono;
    public $num_documento;
    public $nombre_rol;
    public $codigo_rol;
    public $nombre_estado;
    public $codigo_estado;
    public $nombre_tipo_doc;
    public $codigo_tipo_doc;

    public function ObtenerDatos() : string{
    	return $this->nombres . ' ' . $this->apellidos;
    }

    public function EsEditable() : bool{
        return $this->codigo_rol == TipoUsuario::ADMINISTRADOR->value;
    }

    public function TextoAccion() : string{
        return $this->codigo_estado == EstadoRegistro::ACTIVO->value ? 'Desactivar usuario' : 'Activar usuario';
    }
}