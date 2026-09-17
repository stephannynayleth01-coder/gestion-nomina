<?php
session_start();
if (!isset($_SESSION['correo_recuperacion'])) {
    header('Location: recuperar.php');
    exit;
}
require_once 'controllers/RestablecerController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña - Gestión de Nómina</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="contenedor-auth">
        <div class="tarjeta">
            <h2>Restablecer contraseña</h2>

            <?php if ($exito): ?>
            <div class="alerta alerta-exito">
                Tu contraseña se actualizó correctamente.
            </div>
            <a href="index.php" class="boton boton-primario boton-bloque">Ir a iniciar sesión</a>
            <?php else: ?>
            <p class="subtitulo">Ingresa el código enviado a <strong><?= htmlspecialchars (!empty($correo)) ?></strong>
            </p>

            <?php if ($error): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="restablecer.php" method="POST" novalidate>
                <div class="campo">
                    <label for="codigo">Código de verificación</label>
                    <input type="text" id="codigo" name="codigo" maxlength="6" required>
                </div>
                <div class="campo">
                    <label for="nueva_clave">Nueva contraseña (mínimo 8 caracteres)</label>
                    <input type="password" id="nueva_clave" name="nueva_clave" required>
                </div>
                <div class="campo">
                    <label for="confirmar_clave">Confirmar contraseña</label>
                    <input type="password" id="confirmar_clave" name="confirmar_clave" required>
                </div>
                <button type="submit" name="btn_restablecer" class="boton boton-primario">Actualizar contraseña</button>
            </form>
            <?php endif; ?>

            <div class="enlaces">
                <a href="index.php">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>

</html>