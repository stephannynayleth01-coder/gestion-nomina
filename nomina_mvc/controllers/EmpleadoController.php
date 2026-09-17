<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';

$mensaje = "";
$tipo_alerta = "";

if (isset($_POST['btn_registrar'])) {
    $cedula          = trim($_POST['cedula'] ?? '');
    $password        = $_POST['password'] ?? '';
    $nombre_completo = trim($_POST['nombre'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $correo          = trim($_POST['correo'] ?? '');
    $id_centro_costo = (int)($_POST['id_centro_costo'] ?? 1);
    $id_cargo        = (int)($_POST['id_cargo'] ?? 1);
    $salario_base    = (float)($_POST['salario_base'] ?? 0);
    $fecha_ingreso   = date('Y-m-d');

    if ($cedula === '' || $password === '' || $nombre_completo === '' || $correo === '') {
        $mensaje = "Error: Por favor completa todos los campos obligatorios.";
        $tipo_alerta = "error";
    } else {
        $modeloEmpleado = new Empleado($conexion);

        if ($modeloEmpleado->registrar($cedula, $password, $nombre_completo, $telefono, $correo, $id_centro_costo, $id_cargo, $salario_base, $fecha_ingreso)) {
            $mensaje = "Empleado registrado exitosamente.";
            $tipo_alerta = "exito";
        } else {
            $mensaje = "No se pudo registrar el empleado: " . $modeloEmpleado->ultimo_error;
            $tipo_alerta = "error";
        }
    }
}
?>