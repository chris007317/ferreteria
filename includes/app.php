<?php 
date_default_timezone_set('America/Lima');

use Dotenv\Dotenv;
use Model\ActiveRecord;

require __DIR__ . '/../vendor/autoload.php';

// Añadir Dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

require 'funciones.php';
require_once 'conexion.php';


$bd = new Conexion();
$conexion = $bd->conect();

// Conectarnos a la base de datos
ActiveRecord::setDB($conexion);