CREATE DATABASE lluvias;

USE lluvias;

CREATE TABLE lluvias(
fecha_ID DATE PRIMARY KEY ,
cantidad INT(3) NOT NULL CHECK (cantidad BETWEEN 0 AND 400),
CHECK(fecha_ID >= "2025-01-01" AND fecha_ID <= "2025-12-31")
);

-- Obtener meses y cantidades
SELECT 
    MONTH(fecha_ID) AS mes,
    SUM(cantidad) AS total_cantidad
FROM lluvias
GROUP BY MONTH(fecha_ID)
ORDER BY mes;
