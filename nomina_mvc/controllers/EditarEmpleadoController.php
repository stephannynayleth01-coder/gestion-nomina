<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';

$modeloEmpleado = new Empleado($conexion);
$mensaje = "";
$empleado = null;

if (isset($_POST['btn_actualizar'])) {
    $id_usuario = $_POST['id_usuario'];
    $numero_identificacion = trim($_POST['numero_identificacion']);
    $nombre_completo = trim($_POST['nombre_completo']);

    $telefono = trim($_POST['telefono']);
    $correo = trim($_POST['correo']);

    $id_centro_costo = $_POST['id_centro_costo'];
    $id_cargo = $_POST['id_cargo'];
    $sueldo_base = $_POST['sueldo_base'];

    if ($modeloEmpleado->actualizar($id_usuario, $numero_identificacion, $nombre_completo, $telefono, $correo, $id_centro_costo, $id_cargo, $sueldo_base)) {
        $mensaje = "Datos actualizados correctamente.";
    } else {
        $mensaje = "Error al actualizar los datos.";
    }
    $empleado = $modeloEmpleado->obtenerPorId($id_usuario);
} else if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];
    $empleado = $modeloEmpleado->obtenerPorId($id_usuario);

    if (!$empleado) {
        die("Empleado no encontrado en la base de datos.");
    }
} else {
    die("Acceso denegado. Faltan parámetros.");
}
?>