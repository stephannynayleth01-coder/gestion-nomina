<?php
// auth/recuperar.php
// el admin ingresa su correo, el sistema valida
// que exista y genera un código de 6 dígitos con expiración de 15 minutos
// el código se muestra en pantalla simulando que "llegó" al correo
session_start();
require_once __DIR__ . '/../config/conexion.php';

$error = '';
$codigoGenerado = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if ($correo === '') {
        $error = 'Debes ingresar tu correo.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo no es válido.';
    } else {
        $stmt = $conexion->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $usuario = $stmt->get_result()->fetch_assoc();

        if (!$usuario) {
            // basta con este mensaje directo.
            $error = 'No existe una cuenta registrada con ese correo.';
        } else {
            // Código numérico de 6 dígitos, válido por 15 minutos
            $codigo = strval(random_int(100000, 999999));
            $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            $actualizar = $conexion->prepare(
                "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id_usuario = ?"
            );
            $actualizar->bind_param('ssi', $codigo, $expira, $usuario['id_usuario']);
            $actualizar->execute();

            // Guardamos el correo en sesión temporal para usarlo en restablecer.php
            $_SESSION['correo_recuperacion'] = $correo;
            $codigoGenerado = $codigo;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar contraseña - Gestión de Nómina</title>
    <link rel="stylesheet" href="../css/estilos.css">
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
                    <button type="submit" class="boton boton-primario">Generar código</button>
                </form>
            <?php endif; ?>

            <div class="enlaces">
                <a href="login.php">Volver a iniciar sesión</a>
            </div>
        </div>
    </div>
</body>
</html>
