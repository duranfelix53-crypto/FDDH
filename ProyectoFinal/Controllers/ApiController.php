<?php

/**
 * Controlador de la API REST pública del sistema.
 *
 * Expone endpoints para consulta de productos en formato JSON,
 * con soporte CORS para consumo desde aplicaciones externas.
 */

namespace Controllers;

use Models\ProductoModel;

/**
 * Controlador de la API REST pública.
 */
class ApiController
{
    /**
     * Retorna todos los productos en formato JSON.
     *
     * Configura cabeceras CORS, maneja preflight OPTIONS,
     * y devuelve un array con `success`, `data` y `total`.
     *
     * @return void
     */
    public function productos(): void
    {
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, OPTIONS");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $model = new ProductoModel();
        $productos = $model->obtenerTodos();

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'data'    => $productos,
            'total'   => count($productos)
        ]);
    }
}