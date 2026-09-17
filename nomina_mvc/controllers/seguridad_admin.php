<?php
session_start();

// Validamos usando isset que la sesión exista y que el rol sea el correcto (ahora es 'ADMIN' en mayúsculas)
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'ADMIN') {
    // Si no es admin, lo expulsamos al login
    header("Location: ../../index.php");
    exit;
}
?>