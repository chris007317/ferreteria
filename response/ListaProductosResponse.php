<?php 

namespace Response;

Class ListaProductosResponse{
	public $id_producto;
    public $nombre;
    public $precio_venta;
    public $stock;
    public $nombre_almacen;
    public $nombre_categoria;

    public function ObtenerTextoProducto(){
    	return "$this->nombre_almacen / $this->nombre_categoria / $this->nombre - stock($this->stock)";
    }
}