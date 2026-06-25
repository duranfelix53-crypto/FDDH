<?php
namespace Controllers;

use Models\ProductoModel;

class PublicController
{
    private ProductoModel $ProductoModel;

    public function __construct()
    {
        $this->ProductoModel = new ProductoModel();
    }

    public function catalogo(): void
    {
        $termino = trim($_GET['buscar'] ?? '');
        $productos = $this->ProductoModel->buscarPublico($termino);
        require_once __DIR__ . '/../views/public/catalogo.php';
    }
}
?>