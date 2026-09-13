<?php
//se abre la conexión a MySQL.

$host       = 'localhost';
$usuario_bd = 'root';
$clave_bd   = '';
$nombre_bd  = 'gestion_nomina';

$conexion = new mysqli($host, $usuario_bd, $clave_bd, $nombre_bd);
$conexion->set_charset('utf8mb4');

if ($conexion->connect_error) {
    die('Error de conexión a la base de datos: ' . $conexion->connect_error);
}
