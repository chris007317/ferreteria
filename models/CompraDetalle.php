<?php 

namespace Model;

Class CompraDetalle extends ActiveRecord{
	protected static $tabla = 'detallecompra';
	protected static $columnasDB = ['id_detalle_compra', 'id_compra', 'id_producto', 'cantidad', 'precio_unitario'];	
	protected $id_detalle_compra; 
	protected $id_compra; 
	protected $id_producto; 
	protected $cantidad; 
	protected $precio_unitario;

	public function __construct($args = []){
		$this->idNombre = 'id_detalle_compra';
        $this->cantidad = $args['stock'] ?? null;
		$this->precio_unitario = $args['precio_compra'] ?? null;
	}

	public function getIdDetalleCompra() : int{
		return $this->id_detalle_compra;
	}

	public function setIdDetalleCompra($id_detalle_compra) : void{
		$this->id_detalle_compra = $id_detalle_compra;
	}

	public function getIdCompra() : int{
		return $this->id_compra;
	}

	public function setIdCompra($id_compra) : void{
		$this->id_compra = $id_compra;
	}

	public function getIdProducto() : int{
		return $this->id_producto;
	}

	public function setIdProducto($id_producto) : void{
		$this->id_producto = $id_producto;
	}

	public function getCantidad() : int{
		return $this->cantidad;
	}

	public function setCantidad($cantidad) : void{
		$this->cantidad = $cantidad;
	}

	public function getPrecioUnitario() : float{
		return $this->precio_unitario;
	}

	public function setPrecioUnitario($precio_unitario) : void{
		$this->precio_unitario = $precio_unitario;
	}
}