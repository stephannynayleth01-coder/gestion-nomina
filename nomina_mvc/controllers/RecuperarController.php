<?php
require_once __DIR__ . '/../config/conexion.php';

$error = '';
$codigoGenerado = null;

if (isset($_POST['btn_recuperar'])) {
    $correo = trim($_POST['correo'] ?? '');

    if ($correo === '') {
        $error = 'Debes ingresar tu correo.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El formato del correo no es válido.';
    } else {
        $sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 's', $correo);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $usuario = mysqli_fetch_assoc($resultado);

            if (!$usuario) {
                $error = 'No existe una cuenta registrada con ese correo.';
            } else {
                // Código numérico de 6 dígitos, válido por 15 minutos
                $codigo = strval(random_int(100000, 999999));
                $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                $sql_update = "UPDATE usuarios SET token_recuperacion = ?, token_expira = ? WHERE id_usuario = ?";
                $actualizar = mysqli_prepare($conexion, $sql_update);
                mysqli_stmt_bind_param($actualizar, 'ssi', $codigo, $expira, $usuario['id_usuario']);
                mysqli_stmt_execute($actualizar);

                // Guardamos el correo en sesión temporal
                $_SESSION['correo_recuperacion'] = $correo;
                $codigoGenerado = $codigo;
            }
        }
    }
}
?>