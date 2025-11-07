<?php 

namespace Model;

Class SerieVenta extends ActiveRecord{
	protected static $tabla = 'serieventa';
	public static $columnasDB = ['id_serie_venta', 'year', 'serie', 'ultimo_correlativo'];
	protected $id_serie_venta; 
	protected $year; 
	protected $serie; 
	protected $ultimo_correlativo;
	public $idNombre;

	public function __construct($args = []){
		$this->idNombre = 'id_serie_venta';
	}

	public function getIdSerieVenta() {
		return $this->id_serie_venta;
	}

	public function setIdSerieVenta($id_serie_venta){
		$this->id_serie_venta = $id_serie_venta;
	}

	public function getYear() {
		return $this->year;
	}

	public function setYear($year){
		$this->year = $year;
	}

	public function getSerie() {
		return $this->serie;
	}

	public function setSerie($serie){
		$this->serie = $serie;
	}

	public function getUltimoCorrelativo() {
		return $this->ultimo_correlativo;
	}

	public function setUltimoCorrelativo($ultimo_correlativo){
		$this->ultimo_correlativo = $ultimo_correlativo;
	}

	public function incrementarCorrelativo() : void {
		$this->ultimo_correlativo++;
	}

	public function GenerarNumeroVenta(): string{
		$correlativoFormateado = str_pad($this->ultimo_correlativo, 8, '0', STR_PAD_LEFT);
		return "{$this->serie}-{$correlativoFormateado}";
	}	
}