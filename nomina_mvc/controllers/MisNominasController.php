<?php
require_once __DIR__ . '/../config/conexion.php';

$id_usuario = $_SESSION['id_usuario'];

// Consultamos el historial de nóminas maestras asociadas a este empleado específico
$sql_historial = "
    SELECT n.id_nomina, n.descripcion, n.fecha_inicio, n.fecha_fin, n.creado_en 
    FROM nominas n
    JOIN detalle_nomina dn ON n.id_nomina = dn.id_nomina
    WHERE dn.id_usuario = ?
    GROUP BY n.id_nomina
    ORDER BY n.fecha_inicio DESC
";

$stmt = mysqli_prepare($conexion, $sql_historial);
$historial_nominas = [];

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    while ($fila = mysqli_fetch_assoc($resultado)) {
        $historial_nominas[] = $fila;
    }
    mysqli_stmt_close($stmt);
}
?>