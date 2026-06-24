<?php

/**
 * Controlador público del catálogo de productos.
 *
 * Maneja la vista del catálogo con búsqueda por nombre/descripción
 * y paginación de resultados, sin requerir autenticación.
 */

namespace Controllers;

use Models\ProductoModel;

/**
 * Controlador público — catálogo de productos con búsqueda y paginación.
 */
class PublicController
{
    /**
     * Muestra el catálogo público con búsqueda y paginación (9 por página).
     *
     * @return void
     */
    public function catalogo(): void
    {
        $termino = trim($_GET['buscar'] ?? '');
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $limite = 9;
        $offset = ($pagina - 1) * $limite;

        $productoModel = new ProductoModel();
        $productos = $productoModel->buscarPublicoPaginados($termino, $limite, $offset);
        $total = $productoModel->contarBusqueda($termino);
        $totalPaginas = (int) ceil($total / $limite);

        require_once __DIR__ . '/../views/public/catalogo.php';
    }
}