<?php

class Database
{
    private $servidor = "localhost";
    private $user = "root";
    private $pass = "abcdef2020";
    private $dbName = "lluvias";
    private $con;

    public function __construct($dbName)
    {
        $this->dbName = $dbName;
        $this->con = mysqli_connect($this->servidor, $this->user, $this->pass, $this->dbName);
        if (!$this->con) {
            die("Error de conexión");
        }
    }

    public function query($query)
    {
        $result = mysqli_query($this->con, $query);

        if (!$result) {
            echo "Error en la consulta";
        }

        return $result;
    }
}
