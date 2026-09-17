<?php
session_start();

if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['rol'] == 'ADMIN') {
        header("Location: views/admin/listar_empleados.php");
    } else {
        header("Location: views/empleado/perfil_empleado.php");
    }
    exit;
}

// Requerimos el controlador
require_once 'controllers/LoginController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Gestión de Nómina</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="contenedor-auth">
        <div class="tarjeta">
            <h2>Gestión de Nómina</h2>
            <p class="subtitulo">Inicio de sesión del sistema</p>

            <?php if (!empty($mensaje_error)): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($mensaje_error) ?></div>
            <?php endif; ?>

            <form action="index.php" method="POST" novalidate>
                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                        value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" name="btn_login" class="boton boton-primario">Iniciar sesión</button>
            </form>

            <div class="enlaces">
                <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>
</body>

</html>