<?php 
namespace Model;


class Usuario extends ActiveRecord {
	protected static $tabla = 'usuario';
	protected static $columnasDB = ['id_usuario', 'id_persona', 'username', 'email', 'password_hash', 'id_rol_usuario', 'id_estado_usuario'];
	protected $id_usuario;
	protected $id_persona;
	protected $username;
	protected $email;
	protected $password_hash;
	protected $id_rol_usuario;
	protected $id_estado_usuario;

	public function __construct($args = []){
		$this->idNombre = 'id_usuario';
		$this->id_usuario = $args['id_usuario'] ?? null;
		$this->id_persona = $args['id_persona'] ?? null;
        $this->username = $args['username'] ?? null;
		$this->email = $args['email'] ?? null;
		$this->password_hash = $args['password_hash'] ?? null;
		$this->id_rol_usuario = $args['id_rol_usuario'] ?? null;
		$this->id_estado_usuario = $args['id_estado_usuario'] ?? null;
	}

	public function getIdUsuario() {
		return $this->id_usuario;
	}

	public function setIdUsuario($id_usuario) : void{
		$this->id_usuario = $id_usuario;
	}

	public function getIdPersona() : int{
		return $this->id_persona;
	}

	public function setIdPersona($id_persona) : void{
		$this->id_persona = $id_persona;
	}

	public function getUsername() : string{
		return $this->username;
	}

	public function setUsername($username) : void{
		$this->username = $username;
	}

	public function getEmail() : string{
		return $this->email;
	}

	public function setEmail($email) : void{
		$this->email = $email;
	}

	public function getPasswordHash() : string{
		return $this->password_hash;
	}

	public function setPasswordHash($password_hash) : void{
		$this->password_hash = $password_hash;
	}

	public function getIdRolUsuario() : int{
		return $this->id_rol_usuario;
	}

	public function setIdRolUsuario($id_rol_usuario) : void{
		$this->id_rol_usuario = $id_rol_usuario;
	}

	public function getIdEstadoUsuario() : int{
		return $this->id_estado_usuario;
	}

	public function setIdEstadoUsuario($id_estado_usuario) : void{
		$this->id_estado_usuario = $id_estado_usuario;
	}

    public function ValidarUsuario($contraIngresada){
    	if(strlen($this->username) > 20 || strlen($contraIngresada) > 30){
            self::setAlerta('error', 'Usuario y/o contraseña invalida');
        }
    	$resultado = self::whereArrayOne(['username' => $this->username]);

    	if(!$resultado){
    		self::setAlerta('error', 'Usuario no encontrado');
    		return self::$alertas;
    	}
        $this->password_hash = $resultado->getPasswordHash();
        if ($this->comprobarContra($contraIngresada)) {
        	$this->username = $resultado->getUsername();
        	$this->id_usuario = $resultado->getIdUsuario();
        	$this->id_persona = $resultado->getIdPersona();
        }else{
            self::setAlerta('error', 'Contraseña incorrecta');
            return self::$alertas;
        }
        // if(!Roles::esValido($this->getRolUsuario())){
        //     self::setAlerta('error', 'Usuario no fue encontrado');
        // }
    	return self::$alertas;
    }

    public function Validar(){
        if (!filter_var($this->id_persona, FILTER_VALIDATE_INT) || !filter_var($this->id_rol_usuario, FILTER_VALIDATE_INT)){
            self::setAlerta('error', 'Error al realizar la acción');
            return self::$alertas;
        }
        if(empty($this->username) || empty($this->password_hash)){
            self::setAlerta('warning', 'Registre los campos obligatorios');
            return self::$alertas;
        }
        if (!preg_match('/^[a-zA-Z0-9]{6,15}$/', $this->username) && !preg_match('/^[a-zA-Z0-9]{6,15}$/', $this->password_hash)) {
            self::setAlerta('error', 'Usuario o contraseña no valido');
            return self::$alertas;
        }
    }

    public function ValidarContra(){
        if (!preg_match('/^[a-zA-Z0-9]{6,15}$/', $this->password_hash)) {
            self::setAlerta('error', 'Usuario o contraseña no valido');
            return self::$alertas;
        }        
    }

    public function comprobarContra($contraIngresada) : bool {
        return password_verify($contraIngresada, $this->password_hash);
    }

    public function EncriptarContra(){
        $this->password_hash = password_hash($this->password_hash, PASSWORD_BCRYPT);
    }

    public static function ListarUsuario($modelo){
    	$query = "SELECT 
				u.id_usuario, 
			    u.id_persona,
			    u.username,
			    u.email,
			    p.nombres,
			    p.apellidos,
			    p.telefono,
			    p.num_documento,
			    t1.nombre AS nombre_rol,
			    t1.codigo AS codigo_rol,
			    t2.nombre AS nombre_estado,
			    t2.codigo AS codigo_estado,
			    t3.nombre AS nombre_tipo_doc,
			    t3.codigo AS codigo_tipo_doc
			    FROM usuario u
			INNER JOIN personas p ON p.id_persona = u.id_persona
			INNER JOIN tabtab t1 ON t1.id_tabtab = u.id_rol_usuario
			INNER JOIN tabtab t2 ON t2.id_tabtab = u.id_estado_usuario
			INNER JOIN tabtab t3 ON t3.id_tabtab = p.id_tipo_doc
			ORDER BY u.id_usuario DESC;";
		$resultado = self::consultarSQL($query);
		return self::convertirAFilasDeModelo($modelo, $resultado);
    }

    public function ExisteUsuario() : bool{
        $query = "SELECT id_usuario FROM usuario
            WHERE 
                (
                	id_persona = $this->id_persona OR username = '$this->username'
            	) AND
                id_estado_usuario = $this->id_estado_usuario
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        if ($resultado) {
            return true;
        }
        return false;
    }

    public function ExisteNombreUsuario($estado) : bool{
        $query = "SELECT id_usuario FROM usuario
            WHERE 
                id_usuario != $this->id_usuario AND
                username = '$this->username' AND
                id_estado_usuario = $estado
                LIMIT 1;";
        $resultado = self::consultarSQL($query);
        if ($resultado) {
            return true;
        }
        return false;
    }


    public static function Seleccionar($modelo, $id_usuario){
    	$query = "SELECT 
				u.id_usuario, 
			    u.id_persona,
			    u.username,
			    u.email,
			    p.nombres,
			    p.apellidos,
			    p.telefono,
			    p.num_documento,
			    t1.nombre AS nombre_rol,
			    t1.codigo AS codigo_rol,
			    t2.nombre AS nombre_estado,
			    t2.codigo AS codigo_estado,
			    t3.nombre AS nombre_tipo_doc,
			    t3.codigo AS codigo_tipo_doc
			    FROM usuario u
			INNER JOIN personas p ON p.id_persona = u.id_persona
			INNER JOIN tabtab t1 ON t1.id_tabtab = u.id_rol_usuario
			INNER JOIN tabtab t2 ON t2.id_tabtab = u.id_estado_usuario
			INNER JOIN tabtab t3 ON t3.id_tabtab = p.id_tipo_doc
			WHERE u.id_usuario = $id_usuario
			LIMIT 1;";
		$resultado = self::consultarSQL($query);
		$resultado = self::convertirAFilasDeModelo($modelo, $resultado);
		return array_shift($resultado);
    }
}