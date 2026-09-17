<?php
require_once __DIR__ . '/../config/conexion.php';

$mensaje_error = "";

if (isset($_POST['btn_login'])) {
    $correo = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($correo === '' || $password === '') {
        $mensaje_error = 'Debes completar todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $mensaje_error = 'El formato del correo no es válido.';
    } else {
        $sql_usuario = "SELECT id_usuario, nombre_completo, correo, password_hash, rol, estado FROM usuarios WHERE correo = ?";
        $stmt = mysqli_prepare($conexion, $sql_usuario);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $correo);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($fila_usuario = mysqli_fetch_assoc($resultado)) {
                if ($fila_usuario['estado'] === 'INACTIVO') {
                    $mensaje_error = "Esta cuenta se encuentra inactiva.";
                } else {
                    if (password_verify($password, $fila_usuario['password_hash'])) {

                        $_SESSION['id_usuario'] = $fila_usuario['id_usuario'];
                        $_SESSION['nombre_usuario'] = $fila_usuario['nombre_completo'];
                        $_SESSION['rol'] = $fila_usuario['rol'];

                        if ($fila_usuario['rol'] === 'EMPLEADO') {
                            header("Location: views/empleado/perfil_empleado.php");
                        } else {
                            header("Location: views/admin/listar_empleados.php");
                        }
                        exit;
                    } else {
                        $mensaje_error = "La contraseña ingresada es incorrecta.";
                    }
                }
            } else {
                $mensaje_error = "No existe una cuenta registrada con ese correo.";
            }
        } else {
            $mensaje_error = "Error en la consulta a la base de datos.";
        }
    }
}
?>