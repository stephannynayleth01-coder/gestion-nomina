<?php
$host = "localhost";
$usuario = "root";
$password = "";
$base_datos = "gestion_nomina";

// Establecemos la conexión
$conexion = mysqli_connect($host, $usuario, $password, $base_datos);

// miramos si hay error en la conexión
if(!$conexion){
    die("Error de conexión a MySQL: " . mysqli_connect_error());
}

mysqli_set_charset($conexion, "utf8mb4");
?>