<?php 

namespace Response;

use Enum\EstadoProducto;

Class BandejaCompraProductoResponse{
	public $id_producto;
	public $id_categoria;
	public $id_almacen_principal;
	public $id_estado_producto;
	public $nombre;
	public $descripcion;
	public $nombre_almacen;
	public $nombre_categoria;
	public $cantidad;
	public $precio_unitario;
	public $estado_producto;
	public $codigo_producto;

	public function TotalProducto() : string{
		return tipoMonedaPeru($this->cantidad * $this->precio_unitario);
	}

	public function ObtenerBadgeEstado(): string{
		return match ($this->codigo_producto) {
		    EstadoProducto::PENDIENTE->value => 'text-warning',
		    EstadoProducto::REGISTRADO->value => 'text-cyan', 
		    EstadoProducto::EN_ALMACEN->value => 'text-success', 
		    default => '',
		};
	}
}