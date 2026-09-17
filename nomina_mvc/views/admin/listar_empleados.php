<?php
require_once '../../controllers/seguridad_admin.php';
require_once '../../controllers/ListarEmpleadoController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Lista de Empleados</title>
    <link rel="stylesheet" href="../../css/estilo.css">
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

</head>

<body>
    <h2>Listado General de Empleados</h2>

    <?php if (!empty($mensaje_accion)): ?>
    <p><strong><?= htmlspecialchars($mensaje_accion) ?></strong></p>
    <?php endif; ?>

    <nav class="header-nav">
        <a href="registrar_empleado.php" title="Registrar Nuevo Empleado">
            <span class="material-symbols-outlined">how_to_reg</span> Registrar Nuevo Empleado
        </a>
        <a href="../../logout.php" title="Cerrar Sesión">
            <span class="material-symbols-outlined">logout</span> Cerrar Sesión
        </a>
    </nav>

    <table border="1">
        <thead>
            <tr>
                <th>Cédula</th>
                <th>Nombre Completo</th>
                <th>Cargo</th>
                <th>Centro de Costo</th>
                <th>Salario Base</th>
                <th>Estado Nómina</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listaEmpleados)): ?>
            <tr>
                <td colspan="7">No hay empleados registrados.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($listaEmpleados as $empleado): ?>
            <tr>
                <td><?= htmlspecialchars($empleado['numero_identificacion']) ?></td>
                <td><?= htmlspecialchars($empleado['nombre_completo']) ?></td>
                <td><?= htmlspecialchars($empleado['nombre_cargo']) ?></td>
                <td><?= htmlspecialchars($empleado['nombre_centro_costo']) ?></td>
                <td>$<?= number_format($empleado['sueldo_base'], 2) ?></td>

                <td>
                    <?php if ($empleado['nomina_calculada']): ?>
                    <span>Validada</span>
                    <?php else: ?>
                    <span>Pendiente</span>
                    <?php endif; ?>
                </td>

                <td class="acciones-tabla">
                    <!-- VER -->
                    <a href="ver_empleado.php?id=<?= $empleado['id_usuario'] ?>" title="Ver">
                        <span class="material-symbols-outlined">visibility</span>
                    </a> |

                    <!-- EDITAR -->
                    <a href="editar_empleado.php?id=<?= $empleado['id_usuario'] ?>" title="Editar">
                        <span class="material-symbols-outlined">edit</span>
                    </a> |

                    <!-- ELIMINAR -->
                    <a href="listar_empleados.php?eliminar=<?= $empleado['id_usuario'] ?>"
                        onclick="return confirm('¿Seguro de eliminar?');" title="Eliminar">
                        <span class="material-symbols-outlined">delete</span>
                    </a> |

                    <!-- PRÉSTAMO -->
                    <?php if ($empleado['prestamo_activo']): ?>
                    <a href="asignar_prestamo.php?id=<?= $empleado['id_usuario'] ?>" title="Préstamo en curso">
                        <span class="material-symbols-outlined">money_bag</span>
                    </a> |
                    <?php else: ?>
                    <a href="asignar_prestamo.php?id=<?= $empleado['id_usuario'] ?>" title="Asignar Préstamo">
                        <span class="material-symbols-outlined">money_bag</span>
                    </a> |
                    <?php endif; ?>

                    <!-- PDF / LIQUIDAR -->
                    <?php if ($empleado['nomina_calculada']): ?>
                    <a href="generar_pdf.php?id=<?= $empleado['id_usuario'] ?>" target="_blank" title="Descargar PDF">
                        <button type="button"
                            style="cursor: pointer; border: none; background: transparent; padding: 0;">
                            <span class="material-symbols-outlined" style="color: #d9534f;">download</span>
                        </button>
                    </a>
                    <?php else: ?>
                    <a href="generar_nomina.php?id=<?= $empleado['id_usuario'] ?>" title="Liquidar">
                        <span class="material-symbols-outlined">article</span>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>

<footer class="footer-nomina">
    <p class="titulo-footer">Nómina Aplicaciones Web - Grupo 578-301</p>
    <p class="integrantes">Integrantes: Reyes - Tonetti - Fonseca - Valencia - Pinilla</p>
</footer>

</html>