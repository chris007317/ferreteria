<?php 

namespace Model;

Class Compra extends ActiveRecord {
	protected static $tabla = 'compra';
	protected static $columnasDB = ['id_compra', 'id_proveedor', 'id_estado_compra', 'numero_compra', 'fecha', 'subtotal', 'igv', 'total', 'observaciones' , 'compra_eliminado'];
	protected $id_compra; 
	protected $id_proveedor; 
	protected $id_estado_compra; 
	protected $numero_compra; 
	protected $fecha; 
	protected $subtotal; 
	protected $igv; 
	protected $total; 
	protected $observaciones;
	protected $compra_eliminado;

	public function __construct($args = []){
		$this->idNombre = 'id_compra';
		$this->id_compra = $args['id_compra'] ?? null;
		$this->id_proveedor = $args['id_proveedor'] ?? null;
        $this->id_estado_compra = $args['id_estado_compra'] ?? null;
		$this->numero_compra = $args['numero_compra'] ?? null;
		$this->fecha = $args['fecha'] ?? null;
		$this->subtotal = $args['subtotal'] ?? null;
		$this->igv = $args['igv'] ?? null;
		$this->total = $args['total'] ?? null;
		$this->observaciones = $args['observaciones'] ?? null;
	}

	public function getIdCompra() : int{
		return $this->id_compra;
	}

	public function setIdCompra($id_compra) : void{
		$this->id_compra = $id_compra;
	}

	public function getIdProveedor() : int{
		return $this->id_proveedor;
	}

	public function setIdProveedor($id_proveedor) : void{
		$this->id_proveedor = $id_proveedor;
	}

	public function getIdEstadoCompra() : int{
		return $this->id_estado_compra;
	}

	public function setIdEstadoCompra($id_estado_compra) : void{
		$this->id_estado_compra = $id_estado_compra;
	}

	public function getNumeroCompra() : string{
		return $this->numero_compra;
	}

	public function setNumeroCompra($numero_compra) : void{
		$this->numero_compra = $numero_compra;
	}

	public function getFecha() {
		return $this->fecha;
	}

	public function setFecha($fecha) : void{
		$this->fecha = $fecha;
	}

	public function getSubtotal() {
		return $this->subtotal;
	}

	public function setSubtotal($subtotal) : void{
		$this->subtotal = $subtotal;
	}

	public function getIgv() {
		return $this->igv;
	}

	public function setIgv($igv) : void{
		$this->igv = $igv;
	}

	public function getTotal() {
		return $this->total;
	}

	public function setTotal($total) : void{
		$this->total = $total;
	}

	public function getCompraEliminado() : bool{
		return $this->compra_eliminado;
	}

	public function setCompraEliminado($compra_eliminado) : void{
		$this->compra_eliminado = $compra_eliminado;
	}

	public function Validar(){
		if(!preg_match('/^[0-9]{4,20}$/', $this->numero_compra)){
			self::setAlerta('error', 'El número de compras debe contener solo números validos y debe tener por lo menos 4 caracteres.');
            return self::$alertas;
		}
		if($this->total <= 0 || $this->igv <= 0){
			self::setAlerta('error', 'Los valores del total y el igv deben ser mayores a cero.');
            return self::$alertas;
		}
		return self::$alertas;
	}

	public function CalcularSubTotal() : void{
		$this->subtotal = $this->total / (1 + $this->igv);
	}

	public static function BuscarCompras($modelo,  $pagina = 1, $porPagina = 10, $filtros = null){
		$query = "SELECT 
				c.id_compra,
			    c.id_proveedor,
			    c.id_estado_compra,
			    c.numero_compra,
			    c.fecha,
			    c.subtotal,
			    c.total,
			    c.igv,
			    c.observaciones,
				p.razon_social, 
			    p.num_documento, 
			    t.codigo as codigo_estado, 
			    t.nombre as nombre_estado 
			FROM compra c
			INNER JOIN proveedor p ON p.id_proveedor = c.id_proveedor
			INNER JOIN tabtab t ON t.id_tabtab = c.id_estado_compra
			WHERE 
				c.compra_eliminado = FALSE";
		if (!empty($filtros->proveedor)) {
            $texto1 = self::escapar($filtros->proveedor);
            $query .= " AND p.razon_social LIKE '%$texto%'";
        }
        if (!empty($filtros->ruc)) {
            $ruc = self::escapar($filtros->ruc);
            $query .= " AND p.num_documento = '$ruc'";
        }
        if (!empty($filtros->fecha)) {
            $fecha = self::escapar($filtros->fecha);
            $query .= " AND c.fecha = '$fecha'";
        }
        $offset = ($pagina - 1) * $porPagina;
        $query .= " ORDER BY c.id_compra";
        $query .= " LIMIT {$porPagina} OFFSET {$offset}";				
		$resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
	}

	public static function BuscarTotalCompra($filtros){
		$query = "SELECT 
				COUNT(c.id_compra) AS total
			FROM compra c
			INNER JOIN proveedor p ON p.id_proveedor = c.id_proveedor
			INNER JOIN tabtab t ON t.id_tabtab = c.id_estado_compra
			WHERE 
				c.compra_eliminado = FALSE";
		if (!empty($filtros->proveedor)) {
            $texto1 = self::escapar($filtros->proveedor);
            $query .= " AND p.razon_social LIKE '%$texto%'";
        }
        if (!empty($filtros->ruc)) {
            $ruc = self::escapar($filtros->ruc);
            $query .= " AND p.num_documento = '$ruc'";
        }
        if (!empty($filtros->fecha)) {
            $fecha = self::escapar($filtros->fecha);
            $query .= " AND c.fecha = '$fecha'";
        }		
		$resultado = self::consultarSQL($query);
        return isset($resultado[0]['total']) ? (int) $resultado[0]['total'] : 0;
	}

	public function ExisteNumeroCompra() : bool{
        $query = "SELECT id_compra FROM compra
            WHERE 
                id_compra != $this->id_compra AND
                numero_compra = '$this->numero_compra'
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        return $resultado ? true : false;
	}
}