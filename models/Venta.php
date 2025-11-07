<?php 

namespace Model;

Class Venta extends ActiveRecord {
	protected static $tabla = 'venta';
	protected static $columnasDB = ['id_venta', 'id_cliente', 'id_usuario', 'id_tipo_pago', 'numero_venta', 'fecha', 'subtotal', 'descuento', 'total', 'observaciones', 'venta_eliminado', 'igv', 'total_descuento'];
	protected $id_venta; 
	protected $id_cliente; 
	protected $id_usuario; 
	protected $id_tipo_pago; 
	protected $numero_venta; 
	protected $fecha; 
	protected $subtotal; 
	protected $descuento; 
	protected $total; 
	protected $observaciones; 
	protected $venta_eliminado; 
	protected $igv;
	protected $total_descuento;

	public function __construct($args = []){
		$this->idNombre = 'id_venta';
		$this->id_cliente = $args['id_cliente'] ?? null;
		$this->id_usuario = $args['id_usuario'] ?? null;
        $this->id_tipo_pago = $args['id_tipo_pago'] ?? null;
		$this->descuento = $args['descuento'] ?? null;
		$this->fecha = $args['fecha'] ?? null;
		$this->subtotal = $args['subtotal'] ?? null;
		$this->igv = $args['igv'] ?? null;
		$this->total = $args['total'] ?? null;
	}

	public function getIdVenta() : int{
		return $this->id_venta;
	}

	public function setIdVenta($id_venta) : void{
		$this->id_venta = $id_venta;
	}

	public function getIdCliente() : int{
		return $this->id_cliente;
	}

	public function setIdCliente($id_cliente) : void{
		$this->id_cliente = $id_cliente;
	}

	public function getIdUsuario() : int{
		return $this->id_usuario;
	}

	public function setIdUsuario($id_usuario) : void{
		$this->id_usuario = $id_usuario;
	}

	public function getIdTipoPago() : int{
		return $this->id_tipo_pago;
	}

	public function setIdTipoPago($id_tipo_pago) : void{
		$this->id_tipo_pago = $id_tipo_pago;
	}

	public function getNumeroVenta() : string{
		return $this->numero_venta;
	}

	public function setNumeroVenta($numero_venta) : void{
		$this->numero_venta = $numero_venta;
	}

	public function getFecha(){
		return $this->fecha;
	}

	public function setFecha($fecha) : void{
		$this->fecha = $fecha;
	}

	public function getSubtotal() : float{
		return $this->subtotal;
	}

	public function setSubtotal($subtotal) : void{
		$this->subtotal = $subtotal;
	}

	public function getDescuento() : float{
		return $this->descuento;
	}

	public function setDescuento($descuento) : void{
		$this->descuento = $descuento;
	}

	public function getTotal()	: float{
		return $this->total;
	}

	public function setTotal($total) : void{
		$this->total = $total;
	}

	public function getObservaciones() : string{
		return $this->observaciones;
	}

	public function setObservaciones($observaciones) : void{
		$this->observaciones = $observaciones;
	}

	public function getVentaEliminado() : bool{
		return $this->venta_eliminado;
	}

	public function setVentaEliminado($venta_eliminado) : void{
		$this->venta_eliminado = $venta_eliminado;
	}

	public function calcularComplementario() : void{
		$this->total_descuento = $this->total - $this->descuento;
		$this->igv = 0.18;
		$this->subtotal = $this->total * $this->igv;
	}

	public function Validar(){
		if($this->total <= 0){
	    	self::setAlerta('warning', 'Debe seleccionar al menos un producto para la venta');
	        return self::$alertas;
		}
		return self::$alertas;
	}

	public static function ListarVentasRealizadas($modelo, $pagina = 1, $porPagina = 10){
		$query = "SELECT 
				v.id_venta, 
			    v.id_cliente,
			    v.id_usuario,
			    v.id_tipo_pago,
			    v.numero_venta,
			    v.fecha,
			    DATE_FORMAT(v.fecha, '%d/%m/%Y %H:%i') AS fechaVenta,
			    v.subtotal,
			    v.descuento,
			    v.total,
			    v.igv,
			    v.total_descuento,
				p.num_documento, 
			    p.nombres, 
			    p.apellidos,
			    t0.nombre as nombre_tipo_doc,
			    t0.codigo as codigo_tipo_doc,
			    t1.nombre as nombre_pago,
			    t1.codigo as codigo_pago
			FROM venta v
			INNER JOIN cliente c ON c.id_cliente = v.id_cliente
			INNER JOIN personas p ON p.id_persona = c.id_cliente_persona
			INNER JOIN tabtab t0 ON t0.id_tabtab = p.id_tipo_doc
			INNER JOIN tabtab t1 ON t1.id_tabtab = v.id_tipo_pago
			ORDER BY v.id_venta DESC";
		$offset = ($pagina - 1) * $porPagina;			
        $query .= " LIMIT {$porPagina} OFFSET {$offset}";	
		$resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
	}

	public static function TotaVentasRealizadas(){
		$query = "SELECT 
				COUNT(v.id_venta) AS total
			FROM venta v
			INNER JOIN cliente c ON c.id_cliente = v.id_cliente
			INNER JOIN personas p ON p.id_persona = c.id_cliente_persona
			INNER JOIN tabtab t0 ON t0.id_tabtab = p.id_tipo_doc
			INNER JOIN tabtab t1 ON t1.id_tabtab = v.id_tipo_pago
			ORDER BY v.id_venta DESC;";
		$resultado = self::consultarSQL($query);
        return isset($resultado[0]['total']) ? (int) $resultado[0]['total'] : 0;
	}
}