<?php
class Nomina
{
    private $conexion;

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function guardarHistorial($id_usuario, $id_usuario_creo, $fecha_inicio, $fecha_fin, $descripcion, $detalles_calculados)
    {
        mysqli_begin_transaction($this->conexion);
        try {
            $sql_maestra = "INSERT INTO nominas (fecha_inicio, fecha_fin, descripcion, id_usuario_creo, estado) 
                            VALUES (?, ?, ?, ?, 'LIQUIDADA')";
            $stmt_m = mysqli_prepare($this->conexion, $sql_maestra);
            mysqli_stmt_bind_param($stmt_m, "sssi", $fecha_inicio, $fecha_fin, $descripcion, $id_usuario_creo);
            mysqli_stmt_execute($stmt_m);

            $id_nomina = mysqli_insert_id($this->conexion);

            // Insertamos apuntando a id_usuario
            $sql_detalle = "INSERT INTO detalle_nomina (id_nomina, id_usuario, id_concepto, dias, valor) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt_d = mysqli_prepare($this->conexion, $sql_detalle);

            foreach ($detalles_calculados as $detalle) {
                mysqli_stmt_bind_param($stmt_d, "iiidd", $id_nomina, $id_usuario, $detalle['id_concepto'], $detalle['cantidad_dias'], $detalle['valor']);
                mysqli_stmt_execute($stmt_d);
            }

            mysqli_commit($this->conexion);
            return true;
        } catch (Exception $e) {
            mysqli_rollback($this->conexion);
            return false;
        }
    }

    public function verificarNominaMes($id_usuario, $fecha_inicio)
    {
        $sql = "SELECT n.id_nomina FROM nominas n 
                JOIN detalle_nomina dn ON n.id_nomina = dn.id_nomina 
                WHERE dn.id_usuario = ? AND n.fecha_inicio = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "is", $id_usuario, $fecha_inicio);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            return mysqli_num_rows($resultado) > 0;
        }
        return false;
    }
}
?>