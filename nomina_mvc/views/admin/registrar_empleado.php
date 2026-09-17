<?php
require_once '../../controllers/seguridad_admin.php';
require_once '../../controllers/EmpleadoController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Empleado - Nómina</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Registrar Nuevo Empleado</h2>
    <nav class="header-nav">
        <p><a href="listar_empleados.php">Volver a la lista de empleados</a></p>
    </nav>

    <?php if (!empty($mensaje)): ?>
    <p style="color: <?= ($tipo_alerta === 'exito') ? 'green' : 'red'; ?>;">
        <strong><?= htmlspecialchars($mensaje) ?></strong>
    </p>
    <?php endif; ?>

    <form action="registrar_empleado.php" method="POST">
        <table border="0">
            <tr>
                <td><label>Número de Identificación:</label></td>
                <td><input type="text" name="cedula" required></td>
            </tr>
            <tr>
                <td><label>Contraseña (Acceso al sistema):</label></td>
                <td><input type="password" name="password" required></td>
            </tr>
            <tr>
                <td><label>Nombre Completo:</label></td>
                <td><input type="text" name="nombre" required></td>
            </tr>
            <tr>
                <td><label>Teléfono:</label></td>
                <td><input type="text" name="telefono" required></td>
            </tr>
            <tr>
                <td><label>Correo Electrónico (Para Login):</label></td>
                <td><input type="email" name="correo" required></td>
            </tr>
            <tr>
                <td><label>Centro de Costo:</label></td>
                <td>
                    <select name="id_centro_costo" required>
                        <option value="1">Administración</option>
                        <option value="2">Operaciones</option>
                        <option value="3">Ventas</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Cargo:</label></td>
                <td>
                    <select name="id_cargo" required>
                        <option value="1">Gerente</option>
                        <option value="2">Asistente</option>
                        <option value="3">Vendedor</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Salario Base:</label></td>
                <td><input type="number" name="salario_base" step="0.01" min="0" required></td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit" name="btn_registrar">Registrar Empleado</button>
                </td>
            </tr>
        </table>
    </form>
</body>
<footer class="footer-nomina">
    <p class="titulo-footer">Nómina Aplicaciones Web - Grupo 578-301</p>
    <p class="integrantes">Integrantes: Reyes - Tonetti - Fonseca - Valencia - Pinilla</p>
</footer>

</html>