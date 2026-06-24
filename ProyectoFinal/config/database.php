<?php
namespace Config;
/* La clase Database en el espacio de nombres (namespace) 
Config establece una conexión a una base de datos MySQL utilizando PDO en PHP. */
use PDO;
use PDOException;

class Database {
    private string $host = "127.0.0.1";
    private string $dbname = "tienda_mvc";
    private string $username = "root";
    private string $password = "";
    private string $charset = "utf8mb4";

    public function connect() : PDO {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;

            #echo "Conexion exitosa";

        } catch (PDOException $e) {
            die("Error de conexión: ". $e->getMessage());
        }
    }
}