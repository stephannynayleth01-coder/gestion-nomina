<?php
require_once '../../controllers/seguridad_admin.php';
require_once '../../controllers/VerEmpleadoController.php';
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
    <title>Ver Detalles del Empleado</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Detalles del Empleado</h2>

    <nav class="header-nav">
        <p><a href="listar_empleados.php">Volver a la lista de empleados</a></p>
    </nav>

    <!-- Usamos una tabla sin inputs, para que sea de "Solo Lectura" -->
    <table border="1" cellpadding="8">
        <tr>
            <th align="left" width="200">Número de Identificación:</th>
            <td><?= htmlspecialchars($datos_empleado['numero_identificacion']) ?></td>
        </tr>
        <tr>
            <th align="left">Nombre Completo:</th>
            <td><?= htmlspecialchars($datos_empleado['nombre_completo']) ?></td>
        </tr>
        <tr>
            <th align="left">Teléfono:</th>
            <td><?= htmlspecialchars($datos_empleado['telefono']) ?></td>
        </tr>
        <tr>
            <th align="left">Correo Electrónico:</th>
            <td><?= htmlspecialchars($datos_empleado['correo']) ?></td>
        </tr>
        <tr>
            <th align="left">Centro de Costo:</th>
            <td><?= htmlspecialchars($datos_empleado['nombre_centro_costo']) ?></td>
        </tr>
        <tr>
            <th align="left">Cargo:</th>
            <td><?= htmlspecialchars($datos_empleado['nombre_cargo']) ?></td>
        </tr>
        <tr>
            <th align="left">Salario Base Contratado:</th>
            <td>$<?= number_format($datos_empleado['sueldo_base'], 2) ?></td>
        </tr>
        <tr>
            <th align="left">Fecha de Ingreso a la Empresa:</th>
            <td><?= htmlspecialchars($datos_empleado['fecha_ingreso']) ?></td>
        </tr>
        <tr>
            <th align="left">Estado en el Sistema:</th>
            <td>
                <?php if ($datos_empleado['estado'] === 'ACTIVO'): ?>
                <b>ACTIVO</b>
                <?php else: ?>
                <b>INACTIVO</b>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <br>
    <p>
        <a href="editar_empleado.php?id=<?= $datos_empleado['id_usuario'] ?>">
            <button type="button">Ir a Editar este Empleado</button>
        </a>
    </p>
</body>
<footer class="footer-nomina">
    <p class="titulo-footer">Nómina Aplicaciones Web - Grupo 578-301</p>
    <p class="integrantes">Integrantes: Reyes - Tonetti - Fonseca - Valencia - Pinilla</p>
</footer>

</html>