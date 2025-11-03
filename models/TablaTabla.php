<?php 

namespace Model;

Class TablaTabla extends ActiveRecord{
	protected static $tabla = 'tabtab';
	protected static $columnasDB = ['id_tabtab', 'grupo', 'codigo', 'nombre', 'valor'];	
	protected $id_tabtab;
	protected $grupo;
	protected $codigo;
	protected $nombre;
	protected $valor;

	public function getIdTabtab() : int{
		return $this->id_tabtab;
	}

	public function setIdTabtab($id_tabtab) : void{
		$this->id_tabtab = $id_tabtab;
	}

	public function getGrupo() : string{
		return $this->grupo;
	}

	public function setGrupo($grupo) : void{
		$this->grupo = $grupo;
	}

	public function getCodigo() : string{
		return $this->codigo;
	}

	public function setCodigo($codigo) : void{
		$this->codigo = $codigo;
	}

	public function getNombre() : string{
		return $this->nombre;
	}

	public function setNombre($nombre) : void{
		$this->nombre = $nombre;
	}

	public function getValor() : string{
		return $this->valor;
	}

	public function setValor($valor) : void{
		$this->valor = $valor;
	}
}