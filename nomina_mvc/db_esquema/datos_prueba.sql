-- Insertar 4 Empleados de prueba en la tabla unificada 'usuarios'
INSERT INTO usuarios 
(numero_identificacion, nombre_completo, correo, telefono, password_hash, rol, id_cargo, id_centro_costo, sueldo_base, fecha_ingreso, estado) 
VALUES
('1010101010', 'Juan Perez', 'juan@nomina.com', '3100000001', 'PEGA_AQUI_EL_HASH_123456', 'EMPLEADO', 1, 1, 3500000.00, '2023-01-15', 'ACTIVO'),
('2020202020', 'Maria Gomez', 'maria@nomina.com', '3100000002', 'PEGA_AQUI_EL_HASH_123456', 'EMPLEADO', 2, 2, 1800000.00, '2023-03-10', 'ACTIVO'),
('3030303030', 'Carlos Ruiz', 'carlos@nomina.com', '3100000003', 'PEGA_AQUI_EL_HASH_123456', 'EMPLEADO', 3, 3, 1300000.00, '2024-06-01', 'ACTIVO'),
('4040404040', 'Ana Martinez', 'ana@nomina.com', '3100000004', 'PEGA_AQUI_EL_HASH_123456', 'EMPLEADO', 2, 1, 1950000.00, '2024-01-20', 'ACTIVO');
