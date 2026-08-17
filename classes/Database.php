<?php

class Database
{
    private $host = 'localhost';
    private $db_name = 'biblioteca';
    private $username = 'root';
    private $password = '';
    public $conn;

    // Método para obtener la conexión a la base de datos
    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            //echo ("Conexión exitosa a la base de datos");
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
