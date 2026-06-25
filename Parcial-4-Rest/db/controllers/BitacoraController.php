<?php
namespace Controllers;

use Models\BitacoraModel;

class BitacoraController
{
    private BitacoraModel $BitacoraModel;

    public function __construct()
    {
        $this->BitacoraModel = new BitacoraModel();
    }

    private function verificarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    public function index(): void
    {
        $this->verificarSesion();
        $registros = $this->BitacoraModel->obtenerTodos();
        require_once __DIR__ . '/../views/bitacora/index.php';
    }
}
?>