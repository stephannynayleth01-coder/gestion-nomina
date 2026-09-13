<?php
// Punto de entrada de la aplicación. Protegido: si no hay sesión, va al login.
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header('Location: auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del administrador - Gestión de Nómina</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
    <div class="contenedor-panel">
        <header class="encabezado-panel">
            <h1>Gestión de Nómina</h1>
            <div class="usuario-sesion">
                <span>Hola, <?= htmlspecialchars($_SESSION['nombre']) ?></span>
                <a href="auth/logout.php" class="boton boton-secundario">Cerrar sesión</a>
            </div>
        </header>

        <main>
            <p>
                Sesión iniciada correctamente. Aquí es donde tus compañeros
                integrarán los módulos de empleados, nómina y desprendible de pago.
            </p>
        </main>
    </div>
</body>
</html>
