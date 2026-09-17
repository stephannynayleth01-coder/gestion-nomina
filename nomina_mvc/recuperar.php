<?php
session_start();
require_once 'controllers/RecuperarController.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - Gestión de Nómina</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="contenedor-auth">
        <div class="tarjeta">
            <h2>Recuperar contraseña</h2>
            <p class="subtitulo">Ingresa tu correo para generar un código de verificación</p>

            <?php if ($error): ?>
            <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($codigoGenerado): ?>
            <div class="alerta alerta-exito">
                Como este es un proyecto académico sin envío real de correo,
                tu código de verificación es:
                <strong class="codigo-simulado"><?= htmlspecialchars($codigoGenerado) ?></strong>
                <br>Válido por 15 minutos.
            </div>
            <a href="restablecer.php" class="boton boton-primario boton-bloque">Continuar</a>
            <?php else: ?>
            <form action="recuperar.php" method="POST" novalidate>
                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                        value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>
                <button type="submit" name="btn_recuperar" class="boton boton-primario">Generar código</button>
            </form>
            <?php endif; ?>

            <div class="enlaces">
                <a href="index.php">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>

</html>