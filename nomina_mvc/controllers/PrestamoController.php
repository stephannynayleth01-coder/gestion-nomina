<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/Prestamo.php';

$modeloEmpleado = new Empleado($conexion);
$modeloPrestamo = new Prestamo($conexion);

$mensaje = "";
$empleado_seleccionado = null;
$prestamo_activo = null;

if (isset($_POST['btn_asignar_prestamo'])) {
    $id_usuario = $_POST['id_usuario']; // Actualizado a id_usuario
    $monto_desembolso = $_POST['monto_total'];
    $numero_cuotas = $_POST['cuotas_totales'];
    $fecha_desembolso = date('Y-m-d');

    $prestamo_activo = $modeloPrestamo->obtenerPrestamoActivo($id_usuario);

    if ($prestamo_activo) {
        $mensaje = "Error: Este empleado ya tiene un prestamo activo.";
    } else {
        $valor_cuota = $monto_desembolso / $numero_cuotas;
        if ($modeloPrestamo->registrarPrestamo($id_usuario, $monto_desembolso, $numero_cuotas, $valor_cuota, $fecha_desembolso)) {
            $mensaje = "Prestamo asignado exitosamente. Cuota mensual: $" . number_format($valor_cuota, 2);
        } else {
            $mensaje = "Error al guardar el prestamo.";
        }
    }
    $empleado_seleccionado = $modeloEmpleado->obtenerPorId($id_usuario);
    $prestamo_activo = $modeloPrestamo->obtenerPrestamoActivo($id_usuario);
} else if (isset($_GET['id'])) {
    $empleado_seleccionado = $modeloEmpleado->obtenerPorId($_GET['id']);
    if (!$empleado_seleccionado) {
        die("Empleado no encontrado.");
    }
    $prestamo_activo = $modeloPrestamo->obtenerPrestamoActivo($_GET['id']);
} else {
    die("Acceso denegado. Seleccione un empleado desde la lista.");
}
?>