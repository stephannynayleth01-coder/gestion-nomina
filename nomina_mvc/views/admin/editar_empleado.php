<?php require_once '../../controllers/seguridad_admin.php'; ?>
<?php require_once '../../controllers/EditarEmpleadoController.php'; ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Empleado - Nómina</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Editar Datos del Empleado</h2>
    <?php if (!empty($mensaje)): ?>
    <p><strong><?= $mensaje ?></strong></p>
    <?php endif; ?>
    <nav class="header-nav">
        <p><a href="listar_empleados.php">Volver a la lista</a></p>
    </nav>

    <?php if (!empty($empleado)): ?>
    <form action="editar_empleado.php" method="POST">
        <!-- Input oculto usando id_usuario -->
        <input type="hidden" name="id_usuario" value="<?= $empleado['id_usuario'] ?>">

        <table border="1">
            <tr>
                <td><label>Número de Identificación:</label></td>
                <td><input type="text" name="numero_identificacion"
                        value="<?= htmlspecialchars($empleado['numero_identificacion']) ?>" required></td>
            </tr>
            <tr>
                <td><label>Nombre Completo:</label></td>
                <td><input type="text" name="nombre_completo"
                        value="<?= htmlspecialchars($empleado['nombre_completo']) ?>" required></td>
            </tr>
            <tr>
                <td><label>Teléfono:</label></td>
                <td><input type="text" name="telefono" value="<?= htmlspecialchars($empleado['telefono']) ?>" required>
                </td>
            </tr>
            <tr>
                <td><label>Correo Electrónico:</label></td>
                <td><input type="email" name="correo" value="<?= htmlspecialchars($empleado['correo']) ?>" required>
                </td>
            </tr>

            <tr>
                <td><label>Centro de Costo:</label></td>
                <td>
                    <select name="id_centro_costo" required>
                        <option value="1" <?= $empleado['id_centro_costo'] == 1 ? 'selected' : '' ?>>Administración
                        </option>
                        <option value="2" <?= $empleado['id_centro_costo'] == 2 ? 'selected' : '' ?>>Operaciones
                        </option>
                        <option value="3" <?= $empleado['id_centro_costo'] == 3 ? 'selected' : '' ?>>Ventas</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Cargo:</label></td>
                <td>
                    <select name="id_cargo" required>
                        <option value="1" <?= $empleado['id_cargo'] == 1 ? 'selected' : '' ?>>Gerente</option>
                        <option value="2" <?= $empleado['id_cargo'] == 2 ? 'selected' : '' ?>>Asistente</option>
                        <option value="3" <?= $empleado['id_cargo'] == 3 ? 'selected' : '' ?>>Vendedor</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td><label>Salario Base:</label></td>
                <td><input type="number" name="sueldo_base" value="<?= $empleado['sueldo_base'] ?>" step="0.01" min="0"
                        required></td>
            </tr>
            <tr>
                <td colspan="2"><button type="submit" name="btn_actualizar">Guardar Cambios</button></td>
            </tr>
        </table>
    </form>
    <?php endif; ?>
</body>
<footer class="footer-nomina">
    <p class="titulo-footer">Nómina Aplicaciones Web - Grupo 578-301</p>
    <p class="integrantes">Integrantes: Reyes - Tonetti - Fonseca - Valencia - Pinilla</p>
</footer>

</html>