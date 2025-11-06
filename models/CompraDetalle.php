<?php 

namespace Model;

Class CompraDetalle extends ActiveRecord{
	protected static $tabla = 'detallecompra';
	protected static $columnasDB = ['id_detalle_compra', 'id_compra', 'id_producto', 'cantidad', 'precio_unitario', 'costo_unitario'];	
	protected $id_detalle_compra; 
	protected $id_compra; 
	protected $id_producto; 
	protected $cantidad; 
	protected $precio_unitario;
	protected $costo_unitario;

	public function __construct($args = []){
		$this->idNombre = 'id_detalle_compra';
        $this->id_detalle_compra = $args['id_detalle_compra'] ?? null;
        $this->id_compra = $args['id_compra'] ?? null;
        $this->id_producto = $args['id_producto'] ?? null;
        $this->cantidad = $args['cantidad'] ?? null;
		$this->precio_unitario = $args['precio_unitario'] ?? null;
		$this->costo_unitario = $args['costo_unitario'] ?? null;
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

	public function getCostoUnitario() : float {
		return $this->costo_unitario;
	}

	public function setCostoUnitario($costo_unitario) : void{
		$this->costo_unitario = $costo_unitario;
	}
}