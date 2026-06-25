<?php
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class BitacoraModel
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function registrar(int $usuario_id, string $usuario_nombre, string $accion): void
    {
        try {
            $sql = 'INSERT INTO bitacora (usuario_id, usuario_nombre, accion)
                    VALUES (:usuario_id, :usuario_nombre, :accion)';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_nombre', $usuario_nombre);
            $stmt->bindParam(':accion', $accion);
            $stmt->execute();
        } catch (PDOException $e) {
            // silencioso, no interrumpe el flujo
        }
    }

    public function obtenerTodos(): array
    {
        try {
            $sql = 'SELECT * FROM bitacora ORDER BY fecha DESC';
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
?>