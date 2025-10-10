-- Ingresar una fecha dentro del rango permitido
INSERT INTO lluvias VALUES ('2025-01-01',0);

-- Ingresar una fecha anterior a '2025-01-01'
INSERT INTO lluvias VALUES ('2024-01-02',0);

-- Ingresar una fecha posterior a '2025-12-31'
INSERT INTO lluvias VALUES ('2026-01-01',0);

-- Ingresar una fecha repetida
INSERT INTO lluvias VALUES ('2025-01-01',0);

-- Ingresar una cantidad dentro del rango permitido
INSERT INTO lluvias VALUES ('2025-02-02',100);

-- Ingresar una cantidad menor a la cantidad minima permitida
INSERT INTO lluvias VALUES ('2025-02-03',-1);

-- Ingresar una cantidad mayor a la cantidad máxima repetida
INSERT INTO lluvias VALUES ('2025-02-03',500);

-- Ingresar una fecha con valor nulo
INSERT INTO lluvias VALUES (null, 0);


