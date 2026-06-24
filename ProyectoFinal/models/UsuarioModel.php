<?php

/**
 * Modelo de datos para la tabla de usuarios.
 *
 * Encapsula las consultas de autenticación contra la tabla usuarios
 * utilizando PDO con sentencias preparadas.
 */

namespace Models;

use Config\Database;
use PDO;
use PDOException;

/**
 * Consultas de autenticación contra la tabla usuarios.
 */
class UsuarioModel {
    /** @var PDO Conexión a la base de datos */
    private PDO $conexion;

    /**
     * Inicializa la conexión a la base de datos.
     *
     * @return void
     */
    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @param string $username Nombre de usuario a buscar
     * @return array|null Datos del usuario o null si no existe
     */
    public function buscarPorUsername(string $username) : ?array {
        try {
            $sql = 'SELECT * FROM usuarios WHERE username = :username LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $usuario = $stmt->fetch();  
            return $usuario ?: null;
        } catch(PDOException $e) {
            return null;
        }
    }
}