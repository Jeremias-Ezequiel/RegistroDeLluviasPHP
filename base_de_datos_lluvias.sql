CREATE DATABASE lluvias;

USE lluvias;

CREATE TABLE lluvias(
fecha_ID DATE PRIMARY KEY ,
cantidad INT(3) NOT NULL CHECK (cantidad BETWEEN 0 AND 400),
CHECK(fecha_ID >= "2025-01-01" AND fecha_ID <= "2025-12-31")
);

-- Obtener meses y cantidades totales por mes

SELECT 
    MONTH(fecha_ID) AS mes,
    SUM(cantidad) AS total_cantidad
FROM lluvias
GROUP BY MONTH(fecha_ID)
ORDER BY mes;


-- Obtener el mes más lluvioso de todo el año
SELECT
    MONTH(fecha_ID) AS mes,
    SUM(cantidad) AS total_lluvia
FROM
    lluvias
GROUP BY
    mes
HAVING
    total_lluvia = (
        -- Encuentra el valor máximo de todas las sumas mensuales
        SELECT MAX(total_lluvia)
        FROM (
            SELECT SUM(cantidad) AS total_lluvia
            FROM lluvias
            GROUP BY MONTH(fecha_ID)
        ) AS SubConsultaMensual
    )
ORDER BY
    mes;

