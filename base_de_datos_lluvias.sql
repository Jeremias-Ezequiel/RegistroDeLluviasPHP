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


-- Obtenemos solamente el valor máximo en cuanto a la cantidad de lluvia por mes
SELECT
    MAX(total_lluvia) AS max_lluvia
FROM (
    -- Subconsulta: Calcula la suma de lluvia para CADA mes
    SELECT
        SUM(cantidad) AS total_lluvia
    FROM
        lluvias
    GROUP BY
        MONTH(fecha_ID)
) AS LluviasMensual;

