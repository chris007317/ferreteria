<?php 

namespace Response;

Class BandejaVentaResponse{
	public $id_venta; 
	public $id_cliente;
	public $id_usuario;
	public $id_tipo_pago;
	public $numero_venta;
	public $fechaVenta;
	public $subtotal;
	public $descuento;
	public $total;
	public $igv;
	public $total_descuento;
	public $num_documento; 
	public $nombres; 
	public $apellidos;
	public $nombre_tipo_doc;
	public $codigo_tipo_doc;
	public $nombre_pago;
	public $codigo_pago;

	public function ObtenerCliente() : string{
		return "$this->nombres $this->apellidos";
	}
}