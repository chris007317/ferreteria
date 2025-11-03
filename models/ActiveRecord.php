<?php
namespace Model;

use \PDO;
class ActiveRecord {

    // Base DE DATOS
    protected static $db;
    protected static $tabla = '';
    protected static $columnasDB = [];
    // Alertas y Mensajes
    protected static $alertas = [];
    
    // Definir la conexión a la BD - includes/database.php
    public static function setDB($conexion) {
        self::$db = $conexion;
        
    }

    // Setear un tipo de Alerta
    public static function setAlerta($tipo, $mensaje) {
        static::$alertas['tipo'] = $tipo;
        static::$alertas['mensaje'] = $mensaje;
    }

    // Obtener las alertas
    public static function getAlertas() {
        return static::$alertas;
    }

    // Validación que se hereda en modelos
    public function validar() {
        static::$alertas = [];
        return static::$alertas;
    }

    public static function beginTransaction() {
        return self::$db->beginTransaction();
    }

    public static function commit() {
        return self::$db->commit();
    }

    public static function rollBack() {
        return self::$db->rollBack();
    }

    public static function inTransaction() {
        return self::$db->inTransaction();
    }

    // Consulta SQL para crear un objeto en Memoria (Active Record)
    public static function consultarSQL($query) {
        $resultado = self::$db->prepare($query);
        $resultado->execute();
        $registro = $resultado->fetchall(PDO::FETCH_ASSOC);
        // retornar los resultados
        return $registro;
    }

    public static function consultarOneSQL($query){
        $resultado = self::$db->prepare($query);
        $resultado->execute();
        $array = [];
        while($registro = $resultado->fetch(PDO::FETCH_ASSOC)) {
            $array[] = static::crearObjeto($registro);
        }
        // liberar la memoria
        $resultado->closeCursor();
        // retornar los resultados
        return $array;   
    }

    // Crea el objeto en memoria que es igual al de la BD
    protected static function crearObjeto($registro) {
        $objeto = new static;
        foreach($registro as $key => $value ) {
            if(property_exists( $objeto, $key  )) {
                $objeto->$key = $value;
            }
        }
        return $objeto;
    }

    // Identificar y unir los atributos de la BD
    public function atributos() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if($columna === $this->idNombre) continue;
            if (empty($this->$columna) && is_bool($this->$columna)) {
                $atributos[$columna] = 0;
            }else if($this->$columna !== null){
                $atributos[$columna] = $this->$columna;
            }
        }
        return $atributos;
    }

    // Identificar y unir los atributos de la BD
    public function atributosAll() {
        $atributos = [];
        foreach(static::$columnasDB as $columna) {
            if (empty($this->$columna) && is_bool($this->$columna)) {
                $atributos[$columna] = 0;
            }else if($this->$columna !== null){
                $atributos[$columna] = $this->$columna;
            }
        }
        return $atributos;
    }
    // Obtener todos los Registros
    public static function all($columna, $orden = 'DESC') {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY $columna $orden";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    // Edita solo los atributos que tengan valor
    public function updateOnly(){
        $resultado = '';
        if(!is_null($this->{$this->idNombre})) {
            $atributos = $this->atributos();
            // Iterar para ir agregando cada campo de la BD
            $valores = [];
            foreach($atributos as $key => $value) {
                if (!empty($value) || $value === 0) {
                    $valores[] = "{$key}=?";
                }else{
                    unset($atributos[$key]);
                }
            }
            // Consulta SQL
            $query = "UPDATE " . static::$tabla ." SET ";
            $query .=  join(', ', $valores );
            $query .= " WHERE ".$this->idNombre." = '" . $this->{$this->idNombre} . "' ";
            $query .= " LIMIT 1 "; 
            // Actualizar BD
            $update = self::$db->prepare($query);
            $resultado = $update->execute(array_values($atributos));
            return $resultado;
        }
        return $resultado;
    }
    //Hacer una busqueda por varias condiciones
    public static function whereArrayOne($array = [], $orden = '') {
        $query = "SELECT * FROM " . static::$tabla . " WHERE";
        foreach ($array as $key => $value) {
            if ($key == array_key_last($array)) {
                $query .= " $key = '$value'";
            }else{
                $query .= " $key = '$value' AND";
            }
        }
        $query .= " $orden LIMIT 1";
        $resultado = self::consultarOneSQL($query);
        return array_shift( $resultado ) ;
    }
    //Hacer una busqueda por varias condiciones
    public static function whereArrayAll($array = [], $columna = '', $orden = 'DESC') {
        $query = "SELECT * FROM " . static::$tabla . " WHERE";
        foreach ($array as $key => $value) {
            if ($key == array_key_last($array)) {
                $query .= " $key = '$value'";
            }else{
                $query .= " $key = '$value' AND";
            }
        }
        if ($columna != '') $query .= " ORDER BY $columna $orden";
        $resultado = self::consultarSQL($query);
        return $resultado;
    }
    //Hacer una busqueda por varias condiciones
    public static function whereArrayOneOr($array = []) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE";
        foreach ($array as $key => $value) {
            if ($key == array_key_last($array)) {
                $query .= " $key = '$value'";
            }else{
                $query .= " $key = '$value' OR";
            }
        }
        $query .= " LIMIT 1";
        $resultado = self::consultarOneSQL($query);
        return array_shift( $resultado ) ;
    }
    // Busqueda Where con Columna 
    public static function where($columna, $valor) {
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor' LIMIT 1";
        $resultado = self::consultarOneSQL($query);
        return array_shift( $resultado ) ;
    }

    public static function existeRegistro($array = []) {
        $columnas = array_keys($array);
        $selectColumns = implode(", ", $columnas);
        $whereConditions = [];
        foreach ($array as $key => $value) {
            $whereConditions[] = "$key = '$value'";
        }
        $whereClause = implode(" AND ", $whereConditions);
        $query = "SELECT $selectColumns FROM " . static::$tabla . " WHERE $whereClause LIMIT 1";
        $resultado = self::consultarOneSQL($query);
        return array_shift($resultado);
    }
    // Hacer una busqueda de varios elementos
    public static function whereAll($columna, $valor, $colOrden = '', $orden = '') {
        $ordenar = '';
        if ($colOrden != '' && $orden !='') {
            $ordenar = "ORDER BY $colOrden $orden";
        }
        $query = "SELECT * FROM " . static::$tabla . " WHERE $columna = '$valor' $ordenar";
        $resultado = self::consultarSQL($query);
        $resultado = self::ArrObjetos($resultado);
        return $resultado;
    }
    // Guardar datos
    public function guardar() {
        $resultado = '';
        if(!is_null($this->{$this->idNombre})) {
            // actualizar
            $resultado = $this->actualizar();
        } else {
            // Creando un nuevo registro
            $resultado = $this->crear();
        }
        return $resultado;
    }
    // Actualizar el registro
    public function actualizar() {
        // Sanitizar los datos
        $atributos = $this->atributos();

        // Iterar para ir agregando cada campo de la BD
        $valores = [];
        foreach($atributos as $key => $value) {
            if ($value !== null) {
                $valores[] = "$key=?";
            }else{
                unset($atributos[$key]);
            }
        }

        // Consulta SQL
        $query = "UPDATE " . static::$tabla ." SET ";
        $query .=  join(', ', $valores );
        $query .= " WHERE ".$this->idNombre." = '" . $this->{$this->idNombre} . "' ";
        $query .= " LIMIT 1 ";
        // Actualizar BD
        $update = self::$db->prepare($query);
        $resultado = $update->execute(array_values($atributos));
        return $resultado;
    }

    // Sincroniza BD con Objetos en memoria
    public function sincronizar($args=[]) { 
        foreach($args as $key => $value) {
          if(property_exists($this, $key) && !is_null($value)) {
            $this->$key = $value;
          }
        }
    }

    public static function ArrObjetos($arreglo){
        $resultado = [];
        foreach ($arreglo as $key => $args) {
            array_push($resultado, self::crearObjeto($args));
        }
        return $resultado;
    }
    // crea un nuevo registro
    public function crear() {
        // Sanitizar los datos
        $atributos = $this->atributos();

        // Insertar en la base de datos
        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ( ";
        $query .= join(', ', array_map(function($key) {
            return ":$key";
        }, array_keys($atributos)));
        $query .= " ) ";
        $insert = self::$db->prepare($query);
        $resInsert = $insert->execute($atributos);
        if ($resInsert) { 
            $inserId = self::$db->lastInsertId();
        }else{
            $inserId = 0;
        }
        // Resultado de la consulta
        return [
           'resultado' =>  $resInsert,
           'id' => $inserId
        ];
    }

        //Insertar multiples registros
    public function insertAll($arrValues, $filTit){
        $respuesta = false;
        try {
            $query = " INSERT INTO " . static::$tabla . " ( ";
            $query .= join(', ', array_values($filTit));
            $query .= " ) VALUES ( ";
            $query .= join(', ', array_map(function($key) {
                return ":$key";
            }, array_values($filTit)));
            $query .= " ) ";
            self::$db->beginTransaction();
            $insert = self::$db->prepare($query);
            foreach ($arrValues as $key => $value) {
                for ($i=0; $i < count($value) ; $i++) { 
                    $insert->bindParam(':'.$filTit[$i], $value[$i]);
                }
                $respuesta=$insert->execute();
                if (!$respuesta) {
                    return 'error';
                    break;
                }
            }
            self::$db->commit();
            if ($respuesta) {
                return 1;
            }
        }catch (PDOException $e){
            echo $e->getMessage();
            self::$db->rollback();
        }
    }

    public static function consultar($query){
        $resultado = self::$db->prepare($query);
        $res = $resultado->execute();
        $respuesta = $resultado->fetch(PDO::FETCH_ASSOC);
        // liberar la memoria
        $resultado->closeCursor();
        // retornar los resultados
        return $respuesta;   
    }

    public function toArray($model = null) {
        $data = get_object_vars($this); // Obtiene todas las propiedades del objeto actual
        if ($model !== null) {
            $modelProperties = get_object_vars($model);
            return array_intersect_key($data, $modelProperties); // Filtra solo las claves que existen en el modelo de referencia
        }
        return $data; // Retorna todo si no se pasa un modelo de referencia
    }

    public static function joinToModel($modelo, array $tablas, array $camposBase, array $camposJoin, array $filtros, $columna = '', $orden = 'DESC') {
        $objetoModelo = new $modelo();
        $columnasValidas = array_keys(get_object_vars($objetoModelo));
        $query = self::armarQueryJoin($columnasValidas, $tablas, $camposBase, $camposJoin, $filtros);
        if ($columna != '') $query .= " ORDER BY $columna $orden";
        $resultado = self::consultarSQL($query);
        return self::convertirAFilasDeModelo($modelo, $resultado);
    }

    public static function joinToModelOne($modelo, array $tablas, array $camposBase, array $camposJoin, array $filtros) {
        $objetoModelo = new $modelo();
        $columnasValidas = array_keys(get_object_vars($objetoModelo));
        $query = self::armarQueryJoin($columnasValidas, $tablas, $camposBase, $camposJoin, $filtros, 1);
        $resultado = self::consultarSQL($query);
        $objetos = self::convertirAFilasDeModelo($modelo, $resultado);
        return !empty($objetos) ? $objetos[0] : null;
    }

    private static function armarQueryJoin(array $columnasValidas, array $tablas, array $camposBase, array $camposJoin, array $filtros = [], $limit = null) {
        $tablaBase = static::$tabla;
        $columnas = implode(', ', $columnasValidas);
        $query = "SELECT $columnas FROM $tablaBase base";
        foreach ($tablas as $index => $tablaJoin) {
            $campoBase = $camposBase[$index] ?? null;
            $campoJoin = $camposJoin[$index] ?? null;
            if ($campoBase && $campoJoin) {
                $query .= " INNER JOIN $tablaJoin t{$index} ON base.{$campoBase} = t{$index}.{$campoJoin}";
            }
        }
        if (!empty($filtros)) {
            $filtrosArray = [];
            foreach ($filtros as $columna => $valor) {
                $filtrosArray[] = "$columna = '$valor'";
            }
            $query .= " WHERE " . implode(' AND ', $filtrosArray);
        }

        // LIMIT
        if ($limit !== null) {
            $query .= " LIMIT $limit";
        }

        return $query;
    }

    protected static function convertirAFilasDeModelo($modelo, array $resultados) {
        $objetos = [];
        foreach ($resultados as $fila) {
            $objeto = new $modelo();
            foreach ($fila as $columna => $valor) {
                if (property_exists($objeto, $columna)) {
                    $objeto->$columna = $valor;
                }
            }
            $objetos[] = $objeto;
        }
        return $objetos;
    }
    
    public static function whereAndIn(array $andConds = [], array $inConds = [],int $limit= 0) {
        if (!$andConds && !$inConds) {
            // evita SELECT * sin WHERE
            return [];
        }
        $parts = [];
        // — AND —
        foreach ($andConds as $k => $v) {
            $v      = static::escapar($v);          // usa tu método de escape/saneado
            $parts[] = "$k = '$v'";
        }
        // — IN —
        foreach ($inConds as $k => $vals) {
            if (!is_array($vals) || !$vals) continue;
            $vals = array_map(fn($v) => "'".static::escapar($v)."'", $vals);
            $parts[] = "$k IN (".implode(',', $vals).")";
        }
        $sql  = 'SELECT * FROM '.static::$tabla.' WHERE '.implode(' AND ', $parts);
        if ($limit > 0) $sql .= " LIMIT $limit";
        $resultado = static::consultarOneSQL($sql);
        // Usa tu método genérico de consulta
        return $limit === 1 ? (!$resultado ? null : array_shift($resultado)) : $resultado;
    }

    public static function whereAndNotIn(array $andConds = [], array $inConds = [],int $limit= 0) {
        if (!$andConds && !$inConds) {
            // evita SELECT * sin WHERE
            return [];
        }
        $parts = [];
        // — AND —
        foreach ($andConds as $k => $v) {
            $v      = static::escapar($v);          // usa tu método de escape/saneado
            $parts[] = "$k = '$v'";
        }
        // — IN —
        foreach ($inConds as $k => $vals) {
            if (!is_array($vals) || !$vals) continue;
            $vals = array_map(fn($v) => "'".static::escapar($v)."'", $vals);
            $parts[] = "$k NOT IN (".implode(',', $vals).")";
        }
        $sql  = 'SELECT * FROM '.static::$tabla.' WHERE '.implode(' AND ', $parts);
        if ($limit > 0) $sql .= " LIMIT $limit";
        // Usa tu método genérico de consulta
        return $limit === 1
             ? array_shift(static::consultarOneSQL($sql) ?? [])
             : static::consultarSQL($sql);
    }    

    protected static function escapar(string $v): string
    {
        return str_replace("'", "''", $v);
    }

}