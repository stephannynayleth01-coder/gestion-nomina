<?php
// Paso 2 de la recuperación: el admin ingresa el código recibido (mostrado
// en pantalla en el paso anterior) y su nueva contraseña.
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si no viene del flujo de recuperar.php, no debería estar aquí.
if (!isset($_SESSION['correo_recuperacion'])) {
    header('Location: recuperar.php');
    exit;
}

$error  = '';
$exito  = false;
$correo = $_SESSION['correo_recuperacion'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo          = trim($_POST['codigo'] ?? '');
    $nuevaClave      = $_POST['nueva_clave'] ?? '';
    $confirmarClave  = $_POST['confirmar_clave'] ?? '';

    if ($codigo === '' || $nuevaClave === '' || $confirmarClave === '') {
        $error = 'Todos los campos son obligatorios.';
    } elseif (strlen($nuevaClave) < 8) {
        $error = 'La nueva contraseña debe tener al menos 8 caracteres.';
    } elseif ($nuevaClave !== $confirmarClave) {
        $error = 'Las contraseñas no coinciden.';
    } else {
        $stmt = $conexion->prepare(
            "SELECT id_usuario, token_recuperacion, token_expira
             FROM usuarios WHERE correo = ?"
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario || $usuario['token_recuperacion'] === null) {
            $error = 'No hay una solicitud de recuperación activa. Genera un código nuevo.';
        } elseif ($usuario['token_recuperacion'] !== $codigo) {
            $error = 'El código ingresado es incorrecto.';
        } elseif (strtotime($usuario['token_expira']) < time()) {
            $error = 'El código ha expirado. Solicita uno nuevo.';
        } else {
            // Código válido: se actualiza la contraseña con un hash seguro
            // y se invalida el token para que no se pueda reutilizar.
            $nuevoHash = password_hash($nuevaClave, PASSWORD_DEFAULT);

            $actualizar = $conexion->prepare(
                "UPDATE usuarios
                 SET password_hash = ?, token_recuperacion = NULL, token_expira = NULL
                 WHERE id_usuario = ?"
            );
            $actualizar->bind_param('si', $nuevoHash, $usuario['id_usuario']);
            $actualizar->execute();

            unset($_SESSION['correo_recuperacion']);
            $exito = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña - Gestión de Nómina</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedor-auth">
        <div class="tarjeta">
            <h2>Restablecer contraseña</h2>

            <?php if ($exito): ?>
                <div class="alerta alerta-exito">
                    Tu contraseña se actualizó correctamente.
                </div>
                <a href="login.php" class="boton boton-primario boton-bloque">Ir a iniciar sesión</a>
            <?php else: ?>
                <p class="subtitulo">Ingresa el código enviado a <strong><?= htmlspecialchars($correo) ?></strong></p>

                <?php if ($error): ?>
                    <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form action="restablecer.php" method="POST" novalidate>
                    <div class="campo">
                        <label for="codigo">Código de verificación</label>
                        <input type="text" id="codigo" name="codigo" maxlength="6" required>
                    </div>
                    <div class="campo">
                        <label for="nueva_clave">Nueva contraseña</label>
                        <input type="password" id="nueva_clave" name="nueva_clave" required>
                    </div>
                    <div class="campo">
                        <label for="confirmar_clave">Confirmar contraseña</label>
                        <input type="password" id="confirmar_clave" name="confirmar_clave" required>
                    </div>
                    <button type="submit" class="boton boton-primario">Actualizar contraseña</button>
                </form>
            <?php endif; ?>

            <div class="enlaces">
                <a href="login.php">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
