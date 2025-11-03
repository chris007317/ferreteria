<?php 
class Conexion {
    private $host;
    private $usuario;
    private $contra;
    private $db;
    static $conect;
    
    public function __construct() {
        // Inicializar las propiedades dinámicamente
        $this->host = $_ENV['DB_HOST'];
        $this->usuario = $_ENV['DB_USUARIO'];
        $this->contra = $_ENV['DB_CONTRA'];
        $this->db = $_ENV['DB_NOMBRE'];

        $conectionString = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=utf8";
        try {
            self::$conect = new PDO($conectionString, $this->usuario, $this->contra);
            self::$conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            self::$conect = null;
            //echo "ERROR: " . $e->getMessage();
            error_log("Error de conexión a la BD: " . $e->getMessage());
        }
    }

    public function conect() {
        return self::$conect;
    }
}