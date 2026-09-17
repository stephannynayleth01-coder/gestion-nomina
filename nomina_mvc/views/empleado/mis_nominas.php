<?php
require_once '../../controllers/seguridad_empleado.php';
require_once '../../controllers/MisNominasController.php';

/**
 * Declaración de variables importadas del controlador para Intelephense
 * @var array $historial_nominas
 * @var int $id_usuario
 */
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Nóminas</title>
    <link rel="stylesheet" href="../../css/estilo.css">
</head>

<body>
    <h2>Historial de Pagos de Nómina</h2>
    <p><a href="perfil_empleado.php">Volver a Mi Perfil</a></p>

    <table border="1" cellpadding="5">
        <thead>
            <tr>
                <th>Periodo Liquidado</th>
                <th>Fecha de Inicio</th>
                <th>Fecha de Corte</th>
                <th>Fecha de Generación</th>
                <th>Desprendible</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($historial_nominas)): ?>
            <tr>
                <td colspan="5" align="center">Aún no tienes pagos de nómina registrados en el sistema.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($historial_nominas as $nomina): ?>
            <tr>
                <td><?= htmlspecialchars($nomina['descripcion']) ?></td>
                <td><?= htmlspecialchars($nomina['fecha_inicio']) ?></td>
                <td><?= htmlspecialchars($nomina['fecha_fin']) ?></td>
                <td><?= date('Y-m-d H:i', strtotime($nomina['creado_en'])) ?></td>
                <td align="center">
                    <!-- Reutilizamos el archivo de PDF del admin, pasándole el id del usuario en sesión y el ID de la nómina específica -->
                    <a href="../admin/generar_pdf.php?id=<?= $id_usuario ?>&nomina=<?= $nomina['id_nomina'] ?>"
                        target="_blank">
                        <button type="button"> Descargar PDF</button>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>

</html>