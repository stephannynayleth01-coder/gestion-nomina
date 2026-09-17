<?php
session_start();

// Validamos que exista la sesión y que el rol sea 'EMPLEADO'
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'EMPLEADO') {
    // Si no es un empleado o no hay sesión, lo devolvemos al login
    header("Location: ../../index.php");
    exit;
}
?>