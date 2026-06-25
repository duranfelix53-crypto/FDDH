<?php

/* The LogModel class in PHP is used to interact with a database to log user actions. */
namespace Models;

use Config\Database;
use PDO;

class LogModel
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function registrar(string $usuario, string $accion): void
    {

        $sql = "
            INSERT INTO logs (usuario, accion)
            VALUES (:usuario, :accion)
        ";

        $stmt = $this->conexion->prepare($sql);

        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':accion', $accion);

        $stmt->execute();

    }
}