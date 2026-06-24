<?php
/* Esta clase PHP pertenece al espacio de nombres (namespace) 
Controllers y contiene un método catalogo() que recupera y 
muestra productos del catálogo público según un término de búsqueda 
proporcionado por el usuario. */
namespace Controllers;

use Models\ProductoModel;

class PublicController
{
    public function catalogo(): void
    {
        $termino = trim($_GET['buscar'] ?? '');
        $productoModel = new ProductoModel();
        $productos = $productoModel->buscarPublico($termino);
        require_once __DIR__ . '/../config/views/public/catalogo.php';
    }
}