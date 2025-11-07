<?php 

namespace Model;

Class VentaDetalle extends ActiveRecord{
	protected static $tabla = 'detalleventa';
	protected static $columnasDB = ['id_detalle', 'id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'descuento_item', 'subtotal', 'igv', 'precio_descuento'];
	protected $id_detalle; 
	protected $id_venta; 
	protected $id_producto; 
	protected $cantidad; 
	protected $precio_unitario; 
	protected $descuento_item; 
	protected $subtotal; 
	protected $igv;
	protected $precio_descuento;

	public function __construct($args = []){
		$this->idNombre = 'id_detalle';
	}

	public function getIdDetalle(){
		return $this->id_detalle;
	}

	public function setIdDetalle($id_detalle){
		$this->id_detalle = $id_detalle;
	}

	public function getIdVenta(){
		return $this->id_venta;
	}

	public function setIdVenta($id_venta){
		$this->id_venta = $id_venta;
	}

	public function getIdProducto(){
		return $this->id_producto;
	}

	public function setIdProducto($id_producto){
		$this->id_producto = $id_producto;
	}

	public function getCantidad(){
		return $this->cantidad;
	}

	public function setCantidad($cantidad){
		$this->cantidad = $cantidad;
	}

	public function getPrecioUnitario(){
		return $this->precio_unitario;
	}

	public function setPrecioUnitario($precio_unitario){
		$this->precio_unitario = $precio_unitario;
	}

	public function getDescuentoItem(){
		return $this->descuento_item;
	}

	public function setDescuentoItem($descuento_item){
		$this->descuento_item = $descuento_item;
	}

	public function getSubtotal(){
		return $this->subtotal;
	}

	public function setSubtotal($subtotal){
		$this->subtotal = $subtotal;
	}

	public function getIgv(){
		return $this->igv;
	}

	public function setIgv($igv){
		$this->igv = $igv;
	}

	public function InsertarVariosDetalle($detalleVenta, $idVenta) {
		
	    $columnas = ['id_venta', 'id_producto', 'cantidad', 'precio_unitario', 'descuento_item', 'subtotal', 'igv', 'precio_descuento'];
	    
	    $datos = array_map(function($productoVenta) use ($idVenta) {
	        return [
	            $idVenta,
	            $productoVenta->id_producto,
	            $productoVenta->cantidad,
	            $productoVenta->precio_unitario,
	            $productoVenta->descuento_unidad,
	            $productoVenta->sub_total,
	            $productoVenta->igv,
	            $productoVenta->precio_descuento
	        ];
	    }, $detalleVenta);
	    
	    return self::insertAll($datos, $columnas);
	}
}