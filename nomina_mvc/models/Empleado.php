<?php
class Empleado
{
    private $conexion;
    public $ultimo_error = "";

    public function __construct($db)
    {
        $this->conexion = $db;
    }

    public function registrar($cedula, $password, $nombre_completo, $telefono, $correo, $id_centro_costo, $id_cargo, $salario_base, $fecha_ingreso)
    {
        $nombre_completo = trim($nombre_completo);
        $password_hash   = password_hash($password, PASSWORD_BCRYPT);
        $rol             = 'EMPLEADO';

        $sql = "INSERT INTO usuarios 
                (numero_identificacion, nombre_completo, correo, telefono, password_hash, rol, id_cargo, id_centro_costo, sueldo_base, fecha_ingreso, estado) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ACTIVO')";

        $stmt = mysqli_prepare($this->conexion, $sql);
        if (!$stmt) {
            $this->ultimo_error = "Error al preparar la consulta: " . mysqli_error($this->conexion);
            return false;
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssiids",
            $cedula,
            $nombre_completo,
            $correo,
            $telefono,
            $password_hash,
            $rol,
            $id_cargo,
            $id_centro_costo,
            $salario_base,
            $fecha_ingreso
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            return true;
        } else {
            $this->ultimo_error = "Error al ejecutar la inserción: " . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return false;
        }
    }

    public function actualizar($id_usuario, $cedula, $nombre_completo, $telefono, $correo, $id_centro_costo, $id_cargo, $salario_base)
    {
        $nombre_completo = trim($nombre_completo);

        $sql = "UPDATE usuarios 
                SET numero_identificacion = ?, nombre_completo = ?, telefono = ?, correo = ?, id_centro_costo = ?, id_cargo = ?, sueldo_base = ? 
                WHERE id_usuario = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssiidi", $cedula, $nombre_completo, $telefono, $correo, $id_centro_costo, $id_cargo, $salario_base, $id_usuario);
            $resultado = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $resultado;
        }
        return false;
    }

    public function listarTodos()
    {
        $sql = "SELECT u.*, c.nombre_cargo, cc.nombre AS nombre_centro_costo 
            FROM usuarios u 
            LEFT JOIN cargos c ON u.id_cargo = c.id_cargo 
            LEFT JOIN centros_costo cc ON u.id_centro_costo = cc.id_centro_costo 
            WHERE u.rol = 'EMPLEADO' AND u.estado = 'ACTIVO'
            ORDER BY u.id_usuario DESC";
        $resultado = mysqli_query($this->conexion, $sql);

        $empleados = [];
        if ($resultado) {
            while ($fila = mysqli_fetch_assoc($resultado)) {
                $empleados[] = $fila;
            }
        }
        return $empleados;
    }

    public function obtenerPorId($id_usuario)
    {
        $sql = "SELECT u.*, c.nombre_cargo, cc.nombre AS nombre_centro_costo 
                FROM usuarios u 
                LEFT JOIN cargos c ON u.id_cargo = c.id_cargo 
                LEFT JOIN centros_costo cc ON u.id_centro_costo = cc.id_centro_costo 
                WHERE u.id_usuario = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);
            $datos = mysqli_fetch_assoc($resultado);
            mysqli_stmt_close($stmt);
            return $datos;
        }
        return null;
    }

    public function eliminar($id_usuario)
    {
        // Borrado lógico: Cambiamos el estado a INACTIVO 
        $sql = "UPDATE usuarios SET estado = 'INACTIVO' WHERE id_usuario = ?";
        $stmt = mysqli_prepare($this->conexion, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id_usuario);
            $resultado = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            return $resultado;
        }
        return false;
    }
}
?>
