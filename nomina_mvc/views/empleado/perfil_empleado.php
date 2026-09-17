<?php
require_once '../../controllers/seguridad_empleado.php';
require_once '../../controllers/PerfilEmpleadoController.php';

/**
 * Declaración de variables importadas del controlador para Intelephense
 * @var array $datos_empleado
 * @var array|null $prestamo_activo
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mi Perfil - Nómina</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Bienvenido(a), <?= htmlspecialchars($datos_empleado['nombre_completo']) ?></h2>
    <p>
        <a href="mis_nominas.php">Ver Mis Nóminas y Descargar PDF</a> |
        <a href="../../logout.php">Cerrar Sesión</a>
    </p>

    <h3>Mi Información Laboral</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th align="left">Identificación:</th>
            <td><?= htmlspecialchars($datos_empleado['numero_identificacion']) ?></td>
        </tr>
        <tr>
            <th align="left">Teléfono de Contacto:</th>
            <td><?= htmlspecialchars($datos_empleado['telefono']) ?></td>
        </tr>
        <tr>
            <th align="left">Correo de Acceso:</th>
            <td><?= htmlspecialchars($datos_empleado['correo']) ?></td>
        </tr>
        <tr>
            <th align="left">Cargo Actual:</th>
            <td><?= htmlspecialchars($datos_empleado['nombre_cargo']) ?></td>
        </tr>
        <tr>
            <th align="left">Centro de Costo:</th>
            <td><?= htmlspecialchars($datos_empleado['nombre_centro_costo']) ?></td>
        </tr>
        <tr>
            <th align="left">Salario Base Contratado:</th>
            <td>$<?= number_format($datos_empleado['sueldo_base'], 2) ?></td>
        </tr>
    </table>

    <?php if ($prestamo_activo): ?>
    <h3>Estado de mi Préstamo Actual</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th align="left">Monto Solicitado:</th>
            <td>$<?= number_format($prestamo_activo['monto_desembolso'], 2) ?></td>
        </tr>
        <tr>
            <th align="left">Saldo Pendiente:</th>
            <td><b>$<?= number_format($prestamo_activo['saldo_actual'], 2) ?></b></td>
        </tr>
        <tr>
            <th align="left">Valor Cuota (Descuento en Nómina):</th>
            <td>$<?= number_format($prestamo_activo['valor_cuota'], 2) ?></td>
        </tr>
        <tr>
            <th align="left">Progreso:</th>
            <td><?= $prestamo_activo['cuotas_pagadas'] ?> cuota(s) pagada(s) de <?= $prestamo_activo['numero_cuotas'] ?>
            </td>
        </tr>
    </table>
    <?php endif; ?>
</body>

</html>