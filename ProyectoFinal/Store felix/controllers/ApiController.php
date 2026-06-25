<?php

namespace Controllers;

use Config\Database;
use PDOException;


class ApiController
{
    
    public function productos(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $db = new Database();
            $conexion = $db->connect();

            $sql = "SELECT id, sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen, created_at, updated_at
                    FROM productos
                    ORDER BY id DESC";

            $stmt = $conexion->prepare($sql);
            $stmt->execute();

            $productos = $stmt->fetchAll();

            echo json_encode([
                'status' => 'success',
                'total' => count($productos),
                'data' => $productos
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        } catch (PDOException $e) {
            http_response_code(500);

            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Error al consultar productos',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }
}
?>