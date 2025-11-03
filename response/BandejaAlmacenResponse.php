<?php 

namespace Response;

use Enum\EstadoRegistro;

Class BandejaAlmacenResponse{
	public $id_almacen;
	public $id_estado_almacen;
	public $nombre_almacen;
	public $direccion;
	public $nombre;
	public $codigo;

	public function TextoAccion() : string{
        return $this->codigo == EstadoRegistro::ACTIVO->value ? 'Desactivar almacén' : 'Activar almacén';
    }
}