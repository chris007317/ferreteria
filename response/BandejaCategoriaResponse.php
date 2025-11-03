<?php 

namespace Response;

use Enum\EstadoRegistro;

Class BandejaCategoriaResponse {
	public $id_categoria;
    public $nombre;
    public $descripcion;
    public $id_estado_categoria;
    public $nombre_estado;
    public $codigo_estado;

    public function TextoAccion() : string{
        return $this->codigo_estado == EstadoRegistro::ACTIVO->value ? 'Desactivar categoría' : 'Activar categoría';
    }
}