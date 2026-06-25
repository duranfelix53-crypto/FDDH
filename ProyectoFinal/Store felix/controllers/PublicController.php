<?php
/* This PHP class `PublicController` includes a method `catalogo` that retrieves products from a model
based on a search term and then loads a corresponding view. */
namespace Controllers;

use Models\ProductoModel;

class PublicController
{
    public function catalogo(): void
    {
        $termino = trim($_GET['buscar'] ?? '');
        $productoModel = new ProductoModel(); // <-- aquí con mayúscula
        $productos = $productoModel->buscarPublico($termino);
        require_once __DIR__ . '/../views/public/catalogo.php';
    }
}
?>