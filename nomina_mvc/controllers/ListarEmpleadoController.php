<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/Nomina.php';
require_once __DIR__ . '/../models/Prestamo.php';

$modeloEmpleado = new Empleado($conexion);
$modeloNomina = new Nomina($conexion);
$modeloPrestamo = new Prestamo($conexion);
$mensaje_accion = "";

if (isset($_GET['eliminar'])) {
    $id_a_eliminar = $_GET['eliminar'];
    if ($modeloEmpleado->eliminar($id_a_eliminar)) {
        $mensaje_accion = "Empleado eliminado correctamente.";
    } else {
        $mensaje_accion = "Error al intentar eliminar.";
    }
}

$empleadosBrutos = $modeloEmpleado->listarTodos();
$listaEmpleados = [];
$fecha_inicio_mes = date('Y-m-01'); // Primer día del mes actual

foreach ($empleadosBrutos as $emp) {
    $emp['nomina_calculada'] = $modeloNomina->verificarNominaMes($emp['id_usuario'], $fecha_inicio_mes);
    $emp['prestamo_activo'] = $modeloPrestamo->obtenerPrestamoActivo($emp['id_usuario']);
    $listaEmpleados[] = $emp;
}
?>