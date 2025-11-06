<?php 

namespace Model;

Class MovimientoInventario extends ActiveRecord{
	protected static $tabla = 'movimientoinventario';
	protected static $columnasDB = ['id_movimiento', 'id_tipo_movimiento', 'id_producto', 'id_almacen', 'id_venta', 'id_compra', 'id_usuario', 'cantidad', 'stock_anterior', 'stock_actual', 'observaciones'];
	protected $id_movimiento; 
	protected $id_tipo_movimiento; 
	protected $id_producto; 
	protected $id_almacen; 
	protected $id_venta; 
	protected $id_compra; 
	protected $id_usuario; 
	protected $cantidad; 
	protected $stock_anterior; 
	protected $stock_actual; 
	protected $observaciones;

	public function __construct($args = []){
		$this->idNombre = 'id_movimiento';
		$this->id_movimiento = $args['id_movimiento'] ?? null;
	}	

	public function getIdMovimiento() : int{
		return $this->id_movimiento;
	}

	public function setIdMovimiento($id_movimiento) : void{
		$this->id_movimiento = $id_movimiento;
	}

	public function getIdTipoMovimiento() : int{
		return $this->id_tipo_movimiento;
	}

	public function setIdTipoMovimiento($id_tipo_movimiento) : void{
		$this->id_tipo_movimiento = $id_tipo_movimiento;
	}

	public function getIdProducto() : int{
		return $this->id_producto;
	}

	public function setIdProducto($id_producto) : void{
		$this->id_producto = $id_producto;
	}

	public function getIdAlmacen() : int{
		return $this->id_almacen;
	}

	public function setIdAlmacen($id_almacen) : void{
		$this->id_almacen = $id_almacen;
	}

	public function getIdVenta() : int{
		return $this->id_venta;
	}

	public function setIdVenta($id_venta) : void{
		$this->id_venta = $id_venta;
	}

	public function getIdCompra() : int{
		return $this->id_compra;
	}

	public function setIdCompra($id_compra) : void{
		$this->id_compra = $id_compra;
	}

	public function getIdUsuario() : int{
		return $this->id_usuario;
	}

	public function setIdUsuario($id_usuario) : void{
		$this->id_usuario = $id_usuario;
	}

	public function getCantidad() : int{
		return $this->cantidad;
	}

	public function setCantidad($cantidad) : void{
		$this->cantidad = $cantidad;
	}

	public function getStockAnterior() : int{
		return $this->stock_anterior;
	}

	public function setStockAnterior($stock_anterior) : void{
		$this->stock_anterior = $stock_anterior;
	}

	public function getStockActual() : int{
		return $this->stock_actual;
	}

	public function setStockActual($stock_actual) : void{
		$this->stock_actual = $stock_actual;
	}

	public function getObservaciones() : string{
		return $this->observaciones;
	}

	public function setObservaciones($observaciones) : void{
		$this->observaciones = $observaciones;
	}

	public function InsertarVariosMovimientos($datos) : bool{
		$camposMovimiento = ['id_tipo_movimiento', 'id_producto', 'id_almacen', 'id_compra', 'id_usuario', 'cantidad', 'stock_anterior', 'stock_actual'];
		return self::insertAll($datos, $camposMovimiento);
	}
}