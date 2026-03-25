<?php
class Database {
    private $host = "localhost";
    private $adname = "Actividad_3";
    private $username = "root";
    private $password = "123";
    private $connection;

    public function __construct() {
   try {
        $dsn = "mysql:host={$this->host};dbname={$this->username};charset=utf8mb4";
        $this->connection = new PDO($dsn, $this->username, $this->password);

        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error de conexion: " . $e->getMessage());
    }
}


public function getConnection()  {
    return $this->connection;
}
}