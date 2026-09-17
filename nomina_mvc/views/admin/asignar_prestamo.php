<?php
require_once '../../controllers/seguridad_admin.php';
require_once '../../controllers/PrestamoController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Prestamo</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Gestionar Prestamo de Empleado</h2>
    <?php if (!empty($mensaje)): ?>
    <p><strong><?= $mensaje ?></strong></p>
    <?php endif; ?>
    <nav class="header-nav">
        <p><a href="listar_empleados.php">Volver a la lista de empleados</a></p>
    </nav>

    <?php if (!empty($empleado_seleccionado)): ?>
    <p>Empleado Seleccionado: <b><?= $empleado_seleccionado['numero_identificacion'] ?> -
            <?= $empleado_seleccionado['nombre_completo'] ?></b></p>

    <?php if (!empty($prestamo_activo)): ?>
    <h3>Estado del Prestamo en Curso</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th align="left">Fecha de Inicio:</th>
            <td><?= $prestamo_activo['fecha_desembolso'] ?></td>
        </tr>
        <tr>
            <th align="left">Monto Inicial Prestado:</th>
            <td>$<?= number_format($prestamo_activo['monto_desembolso'], 2) ?></td>
        </tr>
        <tr>
            <th align="left">Saldo Actual Pendiente:</th>
            <td><b>$<?= number_format($prestamo_activo['saldo_actual'], 2) ?></b></td>
        </tr>
        <tr>
            <th align="left">Valor Cuota Mensual:</th>
            <td>$<?= number_format($prestamo_activo['valor_cuota'], 2) ?></td>
        </tr>
        <tr>
            <th align="left">Progreso de Cuotas:</th>
            <td><?= $prestamo_activo['cuotas_pagadas'] ?> pagadas de <?= $prestamo_activo['numero_cuotas'] ?> totales
            </td>
        </tr>
        <tr>
            <th align="left">Estado:</th>
            <td><?= strtoupper($prestamo_activo['estado']) ?></td>
        </tr>
    </table>

    <?php else: ?>
    <h3>Asignar Nuevo Prestamo</h3>
    <form action="asignar_prestamo.php" method="POST">
        <!-- Input oculto usando id_usuario -->
        <input type="hidden" name="id_usuario" value="<?= $empleado_seleccionado['id_usuario'] ?>">

        <table border="0">
            <tr>
                <td><label>Monto Total del Prestamo ($):</label></td>
                <td><input type="number" name="monto_total" step="0.01" min="1" required></td>
            </tr>
            <tr>
                <td><label>Numero de Cuotas Mensuales:</label></td>
                <td><input type="number" name="cuotas_totales" min="1" required></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit" name="btn_asignar_prestamo">Guardar Prestamo</button></td>
            </tr>
        </table>
    </form>
    <?php endif; ?>
    <?php endif; ?>
</body>
<footer class="footer-nomina">
    <p class="titulo-footer">Nómina Aplicaciones Web - Grupo 578-301</p>
    <p class="integrantes">Integrantes: Reyes - Tonetti - Fonseca - Valencia - Pinilla</p>
</footer>

</html>