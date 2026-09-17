<?php
// views/admin/generar_pdf.php
require_once __DIR__ . '/../../config/conexion.php';
require_once __DIR__ . '/../../models/Empleado.php';
require_once '../../dompdf/autoload.inc.php';

use Dompdf\Dompdf;

if (!isset($_GET['id'])) {
    die("Error: No se especificó el empleado.");
}

$id_usuario = $_GET['id']; // Actualizado a id_usuario
$modeloEmpleado = new Empleado($conexion);
$empleado = $modeloEmpleado->obtenerPorId($id_usuario);

if (!$empleado) {
    die("Empleado no encontrado.");
}

if (isset($_GET['nomina'])) {
    $id_nomina_especifica = $_GET['nomina'];
    $sql_nomina = "SELECT n.id_nomina, n.descripcion, n.fecha_inicio 
                   FROM nominas n 
                   JOIN detalle_nomina dn ON n.id_nomina = dn.id_nomina 
                   WHERE dn.id_usuario = ? AND n.id_nomina = ? LIMIT 1";
    $stmt_n = mysqli_prepare($conexion, $sql_nomina);
    mysqli_stmt_bind_param($stmt_n, "ii", $id_usuario, $id_nomina_especifica);
} else {
    $sql_nomina = "SELECT n.id_nomina, n.descripcion, n.fecha_inicio 
                   FROM nominas n 
                   JOIN detalle_nomina dn ON n.id_nomina = dn.id_nomina 
                   WHERE dn.id_usuario = ? 
                   ORDER BY n.id_nomina DESC LIMIT 1";
    $stmt_n = mysqli_prepare($conexion, $sql_nomina);
    mysqli_stmt_bind_param($stmt_n, "i", $id_usuario);
}
mysqli_stmt_execute($stmt_n);
$resultado_n = mysqli_stmt_get_result($stmt_n);
$nomina_maestra = mysqli_fetch_assoc($resultado_n);

if (!$nomina_maestra) {
    die("No hay registros de nómina para este empleado en la base de datos.");
}

$sql_detalles = "SELECT id_concepto, dias, valor FROM detalle_nomina WHERE id_nomina = ? AND id_usuario = ?";
$stmt_d = mysqli_prepare($conexion, $sql_detalles);
mysqli_stmt_bind_param($stmt_d, "ii", $nomina_maestra['id_nomina'], $id_usuario);
mysqli_stmt_execute($stmt_d);
$resultado_d = mysqli_stmt_get_result($stmt_d);

$dias = 0;
$salario_prop = 0;
$dias_eps = 0;
$incapacidad_eps = 0;
$dias_arl = 0;
$incapacidad_arl = 0;
$horas_nocturnas = 0;
$recargo_noct = 0;
$horas_dominicales = 0;
$recargo_dom = 0;
$auxilio_trans = 0;
$salud = 0;
$pension = 0;
$fondo_sol = 0;
$cuota_prestamo = 0;

$total_devengado = 0;
$total_deducciones = 0;

while ($fila = mysqli_fetch_assoc($resultado_d)) {
    switch ($fila['id_concepto']) {
        case 1:
            $dias = $fila['dias'];
            $salario_prop = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 2:
            $dias_eps = $fila['dias'];
            $incapacidad_eps = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 3:
            $dias_arl = $fila['dias'];
            $incapacidad_arl = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 4:
            $horas_nocturnas = $fila['dias'];
            $recargo_noct = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 5:
            $horas_dominicales = $fila['dias'];
            $recargo_dom = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 6:
            $auxilio_trans = $fila['valor'];
            $total_devengado += $fila['valor'];
            break;
        case 7:
            $salud = $fila['valor'];
            $total_deducciones += $fila['valor'];
            break;
        case 8:
            $pension = $fila['valor'];
            $total_deducciones += $fila['valor'];
            break;
        case 9:
            $fondo_sol = $fila['valor'];
            $total_deducciones += $fila['valor'];
            break;
        case 10:
            $cuota_prestamo = $fila['valor'];
            $total_deducciones += $fila['valor'];
            break;
    }
}

$neto_pagar = $total_devengado - $total_deducciones;
$sueldo = $empleado['sueldo_base'];
$ibc = $total_devengado - $auxilio_trans;

$prima = $ibc * 0.0833;
$cesantias = $ibc * 0.0833;
$int_cesantias = $cesantias * 0.12;
$vacaciones = $sueldo * 0.0417;
$emp_pension = $ibc * 0.12;
$emp_arl = $ibc * 0.00522;
$emp_ccf = $ibc * 0.04;
$total_costo_empresa = $total_devengado + $prima + $cesantias + $int_cesantias + $vacaciones + $emp_pension + $emp_arl + $emp_ccf;

ob_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Comprobante de Nómina</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>COMPROBANTE DE NÓMINA INDIVIDUAL (DETALLADO)</h2>
    <p>Periodo Liquidado: <?= $nomina_maestra['descripcion'] ?> (<?= $nomina_maestra['fecha_inicio'] ?>)</p>

    <h3>1. INFORMACIÓN DEL TRABAJADOR</h3>
    <table border="1" width="100%" cellpadding="5">
        <tr>
            <th align="left">Identificación:</th>
            <td><?= $empleado['numero_identificacion'] ?></td>
            <th align="left">Nombre Completo:</th>
            <td><?= $empleado['nombre_completo'] ?></td>
        </tr>
        <tr>
            <th align="left">Cargo:</th>
            <td><?= $empleado['nombre_cargo'] ?></td>
            <th align="left">Salario Base Contratado:</th>
            <td>$<?= number_format($sueldo, 2) ?></td>
        </tr>
    </table>

    <br>
    <h3>2. DETALLE DE INGRESOS</h3>
    <table border="1" width="100%" cellpadding="5">
        <tr>
            <th align="left">Concepto</th>
            <th align="left">Cantidad / Base</th>
            <th align="right">Valor Calculado</th>
        </tr>
        <tr>
            <td>Salario Proporcional a Días Trabajados</td>
            <td><?= $dias ?> días</td>
            <td align="right">$<?= number_format($salario_prop, 2) ?></td>
        </tr>
        <tr>
            <td>Pago Incapacidad EPS (Enfermedad Común)</td>
            <td><?= $dias_eps ?> días</td>
            <td align="right">$<?= number_format($incapacidad_eps, 2) ?></td>
        </tr>
        <tr>
            <td>Pago Incapacidad ARL (Riesgo Laboral)</td>
            <td><?= $dias_arl ?> días</td>
            <td align="right">$<?= number_format($incapacidad_arl, 2) ?></td>
        </tr>
        <tr>
            <td>Recargos Nocturnos</td>
            <td><?= $horas_nocturnas ?> hrs</td>
            <td align="right">$<?= number_format($recargo_noct, 2) ?></td>
        </tr>
        <tr>
            <td>Horas Dominicales</td>
            <td><?= $horas_dominicales ?> hrs</td>
            <td align="right">$<?= number_format($recargo_dom, 2) ?></td>
        </tr>
        <tr>
            <td>Auxilio de Transporte (Por Ley)</td>
            <td><?= $dias ?> días proporcionales</td>
            <td align="right">$<?= number_format($auxilio_trans, 2) ?></td>
        </tr>
        <tr>
            <th colspan="2" align="right">SUBTOTAL INGRESOS BRUTOS:</th>
            <th align="right">$<?= number_format($total_devengado, 2) ?></th>
        </tr>
    </table>

    <br>
    <h3>3. DETALLE DE DESCUENTOS</h3>
    <table border="1" width="100%" cellpadding="5">
        <tr>
            <th align="left">Concepto</th>
            <th align="right">Valor Descontado</th>
        </tr>
        <tr>
            <td>Salud (Aporte Empleado)</td>
            <td align="right">$<?= number_format($salud, 2) ?></td>
        </tr>
        <tr>
            <td>Pensión (Aporte Empleado)</td>
            <td align="right">$<?= number_format($pension, 2) ?></td>
        </tr>
        <tr>
            <td>Fondo de Solidaridad Pensional</td>
            <td align="right">$<?= number_format($fondo_sol, 2) ?></td>
        </tr>
        <tr>
            <td>Abono Automático a Préstamo</td>
            <td align="right">$<?= number_format($cuota_prestamo, 2) ?></td>
        </tr>
        <tr>
            <th align="right">SUBTOTAL DESCUENTOS:</th>
            <th align="right">$<?= number_format($total_deducciones, 2) ?></th>
        </tr>
    </table>

    <br>
    <table border="1" width="100%" cellpadding="5">
        <tr>
            <th align="right" width="70%">NETO A PAGAR (CONSIGNACIÓN BANCARIA):</th>
            <th align="right" width="30%">$<?= number_format($neto_pagar, 2) ?></th>
        </tr>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Detalle_Nomina_" . $empleado['numero_identificacion'] . ".pdf", ["Attachment" => true]);
exit;
?>