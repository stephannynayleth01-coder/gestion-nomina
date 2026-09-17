<?php
class Prestamo
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function obtenerPrestamoActivo($id_usuario)
    {
        $sql = "SELECT * FROM prestamos WHERE id_usuario = ? AND estado = 'ACTIVO' LIMIT 1";
        $stmt = mysqli_prepare($this->conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_assoc($resultado);
        }
        return null;
    }

    public function descontarCuota($id_prestamo, $cuota_descontada, $saldo_anterior)
    {
        $nuevo_saldo = $saldo_anterior - $cuota_descontada;
        $nuevo_estado = ($nuevo_saldo <= 0) ? 'PAGADO' : 'ACTIVO';
        if ($nuevo_saldo < 0) $nuevo_saldo = 0;

        $sql = "UPDATE prestamos SET saldo_actual = ?, estado = ?, cuotas_pagadas = cuotas_pagadas + 1 WHERE id_prestamo = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "dsi", $nuevo_saldo, $nuevo_estado, $id_prestamo);
            return mysqli_stmt_execute($stmt);
        }
        return false;
    }

    public function registrarPrestamo($id_usuario, $monto_desembolso, $numero_cuotas, $valor_cuota, $fecha_desembolso)
    {
        $saldo_actual = $monto_desembolso;
        $estado = 'ACTIVO';

        $sql = "INSERT INTO prestamos (id_usuario, monto_desembolso, numero_cuotas, fecha_desembolso, valor_cuota, saldo_actual, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "idissds", $id_usuario, $monto_desembolso, $numero_cuotas, $fecha_desembolso, $valor_cuota, $saldo_actual, $estado);
            if (mysqli_stmt_execute($stmt)) {
                return true;
            }
        }
        return false;
    }
}
?>