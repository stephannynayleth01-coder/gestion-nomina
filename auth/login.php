<?php
session_start();
require_once __DIR__ . '/../config/conexion.php';

// Si ya hay una sesión activa, no tiene sentido ver el login de nuevo
if (isset($_SESSION['id_usuario'])) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    // Validaciones básicas
    if ($correo === '' || $clave === '') {
        $error = 'Debes completar todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo no es válido.';
    } else {
        $stmt = $conexion->prepare(
            "SELECT id_usuario, nombre_completo, password_hash, estado
             FROM usuarios WHERE correo = ?"
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario) {
            // Usuario inexistente
            $error = 'No existe una cuenta registrada con ese correo.';
        } elseif ($usuario['estado'] !== 'ACTIVO') {
            $error = 'Esta cuenta se encuentra inactiva.';
        } elseif (!password_verify($clave, $usuario['password_hash'])) {
            // la clave no coincide con el hash
            $error = 'La contraseña ingresada es incorrecta.';
        } else {
            // se crea la sesión con lo mínimo necesario
            $_SESSION['id_usuario'] = $usuario['id_usuario'];
            $_SESSION['nombre']     = $usuario['nombre_completo'];
            header('Location: ../index.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión - Gestión de Nómina</title>
    <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
    <div class="contenedor-auth">
        <div class="tarjeta">
            <h2>Gestión de Nómina</h2>
            <p class="subtitulo">Inicio de sesión del administrador</p>

            <?php if ($error): ?>
                <div class="alerta alerta-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" novalidate>
                <div class="campo">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>">
                </div>
                <div class="campo">
                    <label for="clave">Contraseña</label>
                    <input type="password" id="clave" name="clave" required>
                </div>
                <button type="submit" class="boton boton-primario">Iniciar sesión</button>
            </form>

            <div class="enlaces">
                <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
            </div>
        </div>
    </div>
</body>
</html>
