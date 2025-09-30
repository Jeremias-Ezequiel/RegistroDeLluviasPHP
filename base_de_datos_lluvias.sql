CREATE DATABASE lluvias;

USE lluvias;

CREATE TABLE lluvias(
fecha_ID DATE PRIMARY KEY ,
cantidad INT(3) NOT NULL CHECK (cantidad >= 0 AND cantidad <= 1500)
);
