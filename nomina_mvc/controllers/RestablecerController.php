<?php
require_once __DIR__ . '/../config/conexion.php';

$error  = '';
$exito  = false;
$correo = $_SESSION['correo_recuperacion'] ?? '';

if (isset($_POST['btn_restablecer'])) {
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
        $sql = "SELECT id_usuario, token_recuperacion, token_expira FROM usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $correo);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);

            if (!$usuario || $usuario['token_recuperacion'] === null) {
                $error = 'No hay una solicitud de recuperación activa. Genera un código nuevo.';
            } elseif ($usuario['token_recuperacion'] !== $codigo) {
                $error = 'El código ingresado es incorrecto.';
            } elseif (strtotime($usuario['token_expira']) < time()) {
                $error = 'El código ha expirado. Solicita uno nuevo.';
            } else {
                // Generamos hash seguro
                $nuevoHash = password_hash($nuevaClave, PASSWORD_BCRYPT);

                // Actualizamos clave y anulamos tokens
                $sql_update = "UPDATE usuarios SET password_hash = ?, token_recuperacion = NULL, token_expira = NULL WHERE id_usuario = ?";
                $actualizar = mysqli_prepare($conexion, $sql_update);
                mysqli_stmt_bind_param($actualizar, 'si', $nuevoHash, $usuario['id_usuario']);
                mysqli_stmt_execute($actualizar);

                unset($_SESSION['correo_recuperacion']);
                $exito = true;
            }
        }
    }
}
?>