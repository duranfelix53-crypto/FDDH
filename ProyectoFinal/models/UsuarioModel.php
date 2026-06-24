<?php

/* La clase UsuarioModel en el espacio de nombres Models se conecta a una base de datos 
y proporciona un método para buscar un usuario por nombre de usuario. */
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class UsuarioModel
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function buscarPorUsername(string $username): ?array
    {
        try {
            $sql = 'SELECT * FROM usuarios WHERE username = :username LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $usuario = $stmt->fetch();
            return $usuario ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }
}