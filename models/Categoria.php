<?php 

namespace Model;

Class Categoria extends ActiveRecord{
	protected static $tabla = 'categoria';
	protected static $columnasDB = ['id_categoria', 'nombre', 'descripcion', 'id_estado_categoria'];
	protected $id_categoria;
	protected $nombre;
	protected $descripcion;
	protected $id_estado_categoria;

	public function __construct($args = []){
		$this->idNombre = 'id_categoria';
		$this->id_categoria = $args['id_categoria'] ?? null;
		$this->nombre = $args['nombre'] ?? null;
        $this->descripcion = $args['descripcion'] ?? null;
		$this->id_estado_categoria = $args['id_estado_categoria'] ?? null;
	}

	public function getIdCategoria() : int{
		return $this->id_categoria;
	}

	public function setIdCategoria($id_categoria) : void{
		$this->id_categoria = $id_categoria;
	}

	public function getNombre() : string{
		return $this->nombre;
	}

	public function setNombre($nombre) : void{
		$this->nombre = $nombre;
	}

	public function getDescripcion() : string{
		return $this->descripcion;
	}

	public function setDescripcion($descripcion) : void{
		$this->descripcion = $descripcion;
	}

	public function getIdEstadoCategoria() : int{
		return $this->id_estado_categoria;
	}

	public function setIdEstadoCategoria($id_estado_categoria) : void{
		$this->id_estado_categoria = $id_estado_categoria;
	}

    public function validar(){
    	if(!isset($this->nombre) || !isset($this->descripcion)){
    		self::setAlerta('error', 'Datos incorrectos.');
    		return self::$alertas;
    	}

    	if(empty($this->nombre)){
    		self::setAlerta('error', 'Ingrese el nombre de la categoría.');
    		return self::$alertas;
    	}

    	if(strlen($this->nombre) > 100){
    		self::setAlerta('error', 'El nombre no debe tener más de 100 caracteres.');
    		return self::$alertas;
    	}

    	if(!empty($this->descripcion) && strlen($this->descripcion) > 255){
    		self::setAlerta('error', 'La descripción de la categoria no debe tener más de 255 caracteres.');
    		return self::$alertas;
    	}
    }

    public static function ListarCategorias($modelo, $pagina = 1, $porPagina = 10, $filtros = null){
    	$query = "SELECT 
			c.id_categoria, 
		    c.nombre, 
		    c.descripcion,
		    c.id_estado_categoria,
		    t.nombre AS nombre_estado,
		    t.codigo AS codigo_estado
		FROM categoria c
		INNER JOIN tabtab t ON t.id_tabtab = c.id_estado_categoria";
        if (!empty($filtros->nombre)) {
            $texto = self::escapar($filtros->nombre);
            $query .= " WHERE c.nombre LIKE '%$texto%'";
        }
        $offset = ($pagina - 1) * $porPagina;
        $query .= " ORDER BY c.id_categoria";
        $query .= " LIMIT {$porPagina} OFFSET {$offset}";
		$resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
    }


    public function ExisteNombreCategoria() : bool{
        $query = "SELECT id_categoria FROM categoria
            WHERE 
                id_categoria != $this->id_categoria AND
                nombre = '$this->nombre'
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        return $resultado ? true : false;
    }

    public static function BuscarTotalCategorias($filtros){
    	$query = "SELECT 
			COUNT(c.id_categoria) AS total
		FROM categoria c
		INNER JOIN tabtab t ON t.id_tabtab = c.id_estado_categoria";
        if (!empty($filtros->nombre)) {
            $texto = self::escapar($filtros->nombre);
            $query .= " WHERE c.nombre LIKE '%$texto%'";
        }
        $resultado = self::consultarSQL($query);
        return isset($resultado[0]['total']) ? (int) $resultado[0]['total'] : 0;
    }
}