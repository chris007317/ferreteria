<?php 

namespace Response;

use Enum\EstadoCompra;

Class BandejaCompraResponse {
	public $id_compra; 
	public $id_estado_compra; 
	public $id_proveedor;
	public $numero_compra; 
	public $fecha; 
	public $subtotal; 
	public $igv; 
	public $total; 
	public $observaciones;
	public $codigo_estado;
	public $nombre_estado;
	public $razon_social;
	public $num_documento;

	public function ObtenerBadgeEstado(): string{
		return match ($this->codigo_estado) {
		    EstadoCompra::PENDIENTE->value => 'text-warning',
		    EstadoCompra::APROBADO->value => 'text-cyan', 
		    EstadoCompra::RECIBIDO->value => 'text-success', 
		    default => '',
		};
	}

	public function ObtenerTextoProducto() : string{
		return match ($this->codigo_estado) {
		    EstadoCompra::PENDIENTE->value => 'Agregar productos',
		    default => 'Ver productos',
		};
	}

	public function ObtenerIconoProducto() : string{
		return match ($this->codigo_estado) {
		    EstadoCompra::PENDIENTE->value => 'fa-solid fa-plus',
		    default => 'fa-solid fa-eye',
		};
	}

	public function SePuedeEliminar() : bool{
		return $this->codigo_estado == EstadoCompra::PENDIENTE->value;
	}
}