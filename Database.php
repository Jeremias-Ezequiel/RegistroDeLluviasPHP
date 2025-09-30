<?php

class Database
{
    private $servidor = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbName = "lluvias";
    private $con;

    public function __construct($dbName)
    {
        $this->dbName = $dbName;
        $this->con = mysqli_connect($this->servidor, $this->user, $this->pass, $this->dbName);
    }

    public function query($query)
    {
        return mysqli_query($this->con, $query);
    }
}
