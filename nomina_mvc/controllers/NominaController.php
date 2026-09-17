<?php
require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/Prestamo.php';
require_once __DIR__ . '/../models/Nomina.php';

$modeloEmpleado = new Empleado($conexion);
$modeloPrestamo = new Prestamo($conexion);
$modeloNomina = new Nomina($conexion);

$mensaje = "";
$empleado_seleccionado = null;

define('SMLV', 1750905);
define('AUXILIO_TRANSPORTE', 249095);

if (isset($_POST['btn_calcular_nomina'])) {
    $fecha_inicio = date('Y-m-01');
    $fecha_fin = date('Y-m-t');
    $descripcion = "Nómina " . date('M Y');

    $id_usuario = $_POST['id_usuario']; // Actualizado a id_usuario
    @session_start();
    $id_usuario_creo = $_SESSION['id_usuario'] ?? 1;

    $empleado = $modeloEmpleado->obtenerPorId($id_usuario);
    $sueldo = $empleado['sueldo_base'];

    $dias = $_POST['dias_laborados'];
    $dias_eps = $_POST['dias_eps'] ?? 0;
    $dias_arl = $_POST['dias_arl'] ?? 0;
    $total_dias = $dias + $dias_eps + $dias_arl;

    if ($total_dias > 30) {
        $mensaje = "Error: La suma total ($total_dias) no puede superar los 30 dias.";
    } else if ($modeloNomina->verificarNominaMes($id_usuario, $fecha_inicio)) {
        $mensaje = "Error: Este empleado ya tiene una nomina liquidada para este mes.";
    } else {
        $horas_nocturnas = $_POST['horas_nocturnas'] ?? 0;
        $horas_dominicales = $_POST['horas_dominicales'] ?? 0;

        $salario_prop = ($sueldo / 30) * $dias;
        $incapacidad_eps = ($sueldo / 30) * $dias_eps * 0.6667;
        $incapacidad_arl = ($sueldo / 30) * $dias_arl * 1.0;

        $valor_hora = $sueldo / 240;
        $recargo_noct = $valor_hora * $horas_nocturnas * 0.35;
        $recargo_dom = $valor_hora * $horas_dominicales * 1.75;
        $auxilio_trans = ($sueldo <= (SMLV * 2)) ? (AUXILIO_TRANSPORTE / 30) * $dias : 0;

        $total_devengado = $salario_prop + $incapacidad_eps + $incapacidad_arl + $recargo_noct + $recargo_dom + $auxilio_trans;

        $ibc = $total_devengado - $auxilio_trans;
        $salud = $ibc * 0.04;
        $pension = $ibc * 0.04;
        $fondo_sol = ($ibc >= (SMLV * 4)) ? $ibc * 0.01 : 0;

        $cuota_prestamo = 0;
        $prestamo_activo = $modeloPrestamo->obtenerPrestamoActivo($id_usuario);

        if ($prestamo_activo) {
            $saldo_actual = $prestamo_activo['saldo_actual'];
            $valor_cuota_teorica = $prestamo_activo['valor_cuota'];
            $cuota_prestamo = ($saldo_actual <= $valor_cuota_teorica) ? $saldo_actual : $valor_cuota_teorica;
            $modeloPrestamo->descontarCuota($prestamo_activo['id_prestamo'], $cuota_prestamo, $saldo_actual);
        }

        $detalles_calculados = [
            ['id_concepto' => 1, 'cantidad_dias' => $dias, 'valor' => $salario_prop],
            ['id_concepto' => 2, 'cantidad_dias' => $dias_eps, 'valor' => $incapacidad_eps],
            ['id_concepto' => 3, 'cantidad_dias' => $dias_arl, 'valor' => $incapacidad_arl],
            ['id_concepto' => 4, 'cantidad_dias' => $horas_nocturnas, 'valor' => $recargo_noct],
            ['id_concepto' => 5, 'cantidad_dias' => $horas_dominicales, 'valor' => $recargo_dom],
            ['id_concepto' => 6, 'cantidad_dias' => $dias, 'valor' => $auxilio_trans],
            ['id_concepto' => 7, 'cantidad_dias' => 0, 'valor' => $salud],
            ['id_concepto' => 8, 'cantidad_dias' => 0, 'valor' => $pension],
            ['id_concepto' => 9, 'cantidad_dias' => 0, 'valor' => $fondo_sol],
        ];

        if ($cuota_prestamo > 0) {
            $detalles_calculados[] = ['id_concepto' => 10, 'cantidad_dias' => 0, 'valor' => $cuota_prestamo];
        }

        if ($modeloNomina->guardarHistorial($id_usuario, $id_usuario_creo, $fecha_inicio, $fecha_fin, $descripcion, $detalles_calculados)) {
            $mensaje = "Nomina calculada y guardada para: " . $empleado['nombre_completo'];
        } else {
            $mensaje = "Error al guardar la nomina en la base de datos.";
        }
    }
    $empleado_seleccionado = $empleado;
} else if (isset($_GET['id'])) {
    $empleado_seleccionado = $modeloEmpleado->obtenerPorId($_GET['id']);
}
?>