<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/Prestamo.php';

$id_usuario = $_SESSION['id_usuario'];

$modeloEmpleado = new Empleado($conexion);
$modeloPrestamo = new Prestamo($conexion);

// Obtenemos los datos cruzados (con nombre de cargo y centro de costo)
$datos_empleado = $modeloEmpleado->obtenerPorId($id_usuario);
$prestamo_activo = $modeloPrestamo->obtenerPrestamoActivo($id_usuario);

if (!$datos_empleado) {
    die("Error: No se pudo cargar la información del perfil.");
}
?>