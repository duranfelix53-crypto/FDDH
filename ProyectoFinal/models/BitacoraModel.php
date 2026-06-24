<?php
/* La clase BitacoraModel en el espacio de nombres (namespace) Models se encarga de 
registrar acciones con información del usuario, la acción, el detalle y la dirección 
IP en una tabla de la base de datos, y proporciona métodos para registrar nuevos 
registros y obtener todos los logs ordenados por fecha. */
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class BitacoraModel {
    private PDO $conexion;

    public function __construct() {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function registrar(string $usuario, string $accion, string $detalle = ""): void {
        try {
            $sql = "INSERT INTO bitacora (usuario, accion, detalle, ip)
                    VALUES (:usuario, :accion, :detalle, :ip)";
            $stmt = $this->conexion->prepare($sql);
            $ip = $_SERVER["REMOTE_ADDR"] ?? "unknown";
            $stmt->bindParam(":usuario", $usuario);
            $stmt->bindParam(":accion",  $accion);
            $stmt->bindParam(":detalle", $detalle);
            $stmt->bindParam(":ip",      $ip);
            $stmt->execute();
        } catch (PDOException $e) {
      
        }
    }

    public function obtenerTodos(): array {
        try {
            $sql = "SELECT * FROM bitacora ORDER BY fecha DESC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}