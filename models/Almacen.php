<?php 

namespace Model;

Class Almacen extends ActiveRecord{
	protected static $tabla = 'almacen';
	protected static $columnasDB = ['id_almacen', 'id_estado_almacen', 'nombre_almacen', 'direccion',];
	protected $id_almacen;
	protected $id_estado_almacen;
	protected $nombre_almacen;
	protected $direccion;

	public function __construct($args = []){
		$this->idNombre = 'id_almacen';
		$this->id_almacen = $args['id_almacen'] ?? null;
		$this->id_estado_almacen = $args['id_estado_almacen'] ?? null;
        $this->nombre_almacen = $args['nombre_almacen'] ?? null;
		$this->direccion = $args['direccion'] ?? null;
	}

	public function getIdAlmacen() : int{
		return $this->id_almacen;
	}

	public function setIdAlmacen($id_almacen) : void{
		$this->id_almacen = $id_almacen;
	}

	public function getIdEstadoAlmacen() : int{
		return $this->id_estado_almacen;
	}

	public function setIdEstadoAlmacen($id_estado_almacen) : void{
		$this->id_estado_almacen = $id_estado_almacen;
	}

	public function getNombreAlmacen() : string{
		return $this->nombre_almacen;
	}

	public function setNombreAlmacen($nombre_almacen) : void{
		$this->nombre_almacen = $nombre_almacen;
	}

	public function getDireccion() : string{
		return $this->direccion;
	}

	public function setDireccion($direccion) : void{
		$this->direccion = $direccion;
	}

	public function Validar(){
	    if (empty($this->nombre_almacen) || empty($this->direccion)) {
	        self::setAlerta('error', 'Todos los campos son obligatorios');
	        return self::$alertas;
	    }
	    if (
	        !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 ,.\-\-#\/()°]{3,150}$/u', $this->direccion) ||
	        !preg_match('/^[A-Za-zÁÉÍÓÚáéíóúÑñ0-9 &.,\-\/()°]{3,150}$/u', $this->nombre_almacen)
	    ) {
	        self::setAlerta('error', 'No ingrese caracteres especiales.');
	        return self::$alertas;
	    }
	    return self::$alertas;
	}

	public function ExisteNombreAlmacen() : bool{
        $query = "SELECT id_almacen FROM almacen
            WHERE 
                id_almacen != $this->id_almacen AND
                nombre_almacen = '$this->nombre_almacen'
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        return $resultado ? true : false;
	}
}