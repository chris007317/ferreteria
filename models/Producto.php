<?php 

namespace Model;

Class Producto extends ActiveRecord{
	protected static $tabla = 'producto';
	protected static $columnasDB = ['id_producto', 'id_categoria', 'id_almacen_principal', 'id_estado_producto', 'nombre', 'descripcion', 'precio_compra', 'precio_venta', 'stock', 'stock_minimo', 'producto_eliminado'];
	protected $id_producto; 
	protected $id_categoria; 
	protected $id_almacen_principal; 
	protected $id_estado_producto; 
	protected $nombre; 
	protected $descripcion; 
	protected $precio_compra; 
	protected $precio_venta; 
	protected $stock; 
	protected $stock_minimo; 
	protected $producto_eliminado;

	public function __construct($args = []){
		$this->idNombre = 'id_producto';
		$this->id_producto = $args['id_producto'] ?? null;
		$this->id_categoria = $args['id_categoria'] ?? null;
        $this->id_almacen_principal = $args['id_almacen_principal'] ?? null;
		$this->id_estado_producto = $args['id_estado_producto'] ?? null;
		$this->nombre = $args['nombre'] ?? null;
		$this->descripcion = $args['descripcion'] ?? null;
		$this->precio_compra = $args['precio_compra'] ?? 0;
		$this->precio_venta = $args['precio_venta'] ?? 0;
		$this->stock = $args['stock'] ?? null;
	}	

	public function getIdProducto(){
		return $this->id_producto;
	}

	public function setIdProducto($id_producto){
		$this->id_producto = $id_producto;
	}

	public function getIdCategoria(){
		return $this->id_categoria;
	}

	public function setIdCategoria($id_categoria){
		$this->id_categoria = $id_categoria;
	}

	public function getIdAlmacenPrincipal(){
		return $this->id_almacen_principal;
	}

	public function setIdAlmacenPrincipal($id_almacen_principal){
		$this->id_almacen_principal = $id_almacen_principal;
	}

	public function getIdEstadoProducto(){
		return $this->id_estado_producto;
	}

	public function setIdEstadoProducto($id_estado_producto){
		$this->id_estado_producto = $id_estado_producto;
	}

	public function getNombre(){
		return $this->nombre;
	}

	public function setNombre($nombre){
		$this->nombre = $nombre;
	}

	public function getDescripcion(){
		return $this->descripcion;
	}

	public function setDescripcion($descripcion){
		$this->descripcion = $descripcion;
	}

	public function getPrecioCompra(){
		return $this->precio_compra;
	}

	public function setPrecioCompra($precio_compra){
		$this->precio_compra = $precio_compra;
	}

	public function getPrecioVenta(){
		return $this->precio_venta;
	}

	public function setPrecioVenta($precio_venta){
		$this->precio_venta = $precio_venta;
	}

	public function getStock(){
		return $this->stock;
	}

	public function setStock($stock){
		$this->stock = $stock;
	}

	public function getStockMinimo(){
		return $this->stock_minimo;
	}

	public function setStockMinimo($stock_minimo){
		$this->stock_minimo = $stock_minimo;
	}

	public function getProductoEliminado(){
		return $this->producto_eliminado;
	}

	public function setProductoEliminado($producto_eliminado){
		$this->producto_eliminado = $producto_eliminado;
	}

	public function getArgs(){
		return $this->args;
	}

	public function setArgs($args){
		$this->args = $args;
	}

	public function Validar(){
		if(empty($this->nombre)){
			self::setAlerta('error', 'El nombre es un dato obligaotrio.');
	        return self::$alertas;
		}
		if(!preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 &.,\-\/()°]{3,150}$/u', $this->nombre)){
			self::setAlerta('error', 'No ingrese caracteres especiales en el nombre.');
	        return self::$alertas;
		}
		// if($this->precio_compra <= 0 || $this->precio_venta <= 0 || $this->stock <= 0){
		// 	self::setAlerta('error', 'El precio de venta, precio de compra o cantidad del producto deben ser mayores a cero.');
	    //     return self::$alertas;
		// }
		return self::$alertas;
	}

	public static function ListarProductoCompra($modelo, $idCompra){
		$query = "SELECT 
				p.id_producto,
			    p.id_categoria,
			    p.id_almacen_principal,
			    p.id_estado_producto,
			    p.nombre,
			    p.descripcion,
			    p.precio_venta,
			    p.stock,
			    a.nombre_almacen,
			    c.nombre AS nombre_categoria,
			    dp.cantidad,
			    dp.precio_unitario,
			    dp.costo_unitario,
			    t.nombre AS estado_producto,
			    t.codigo AS codigo_producto
			FROM producto p
			INNER JOIN detallecompra dp ON dp.id_producto = p.id_producto
			INNER JOIN almacen a ON a.id_almacen = p.id_almacen_principal
			INNER JOIN categoria c ON c.id_categoria = p.id_categoria
			INNER JOIN tabtab t ON t.id_tabtab = id_estado_producto
			WHERE 
				p.producto_eliminado = FALSE AND
				dp.id_compra = $idCompra
			ORDER BY p.id_producto DESC;";
		$resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
	}

	public static function EditarEstadoProductosCompra($estadoNuevo, $estadoActual, $ids){
		$query = "UPDATE producto 
			SET id_estado_producto = $estadoNuevo WHERE id_estado_producto = $estadoActual AND id_producto IN ($ids);";
    	$actualizar = self::$db->prepare($query);
        $resultado = $actualizar->execute();
        return $resultado;
	}

	public static function ListarProductosActivos($modelo, $estado, $estadoProducto){
		$query = "SELECT 
				p.id_producto, 
			    p.nombre, 
			    p.precio_venta, 
			    p.stock,
			    a.nombre_almacen,
    			c.nombre AS nombre_categoria
			FROM producto p
			INNER JOIN almacen a ON a.id_almacen = p.id_almacen_principal
			INNER JOIN categoria c ON c.id_categoria = p.id_categoria
			WHERE 
				p.producto_eliminado = FALSE AND
				p.id_estado_producto = $estadoProducto AND
				c.id_estado_categoria = $estado AND
			    a.id_estado_almacen = $estado;";
	    $resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
	}

	public function nuevoEstock($cantidad){
		$this->stock = $this->stock - $cantidad;
	}
}