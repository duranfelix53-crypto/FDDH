<?php

require_once __DIR__ . '/config/Autoload.php';

use Controllers\AuthController;
use Controllers\ProductoController;
use Controllers\PublicController;
use Controllers\BitacoraController;
use Controllers\ApiController;

$route = $_GET['route'] ?? 'catalogo';

$authController = new AuthController();
$productoController = new ProductoController();
$publicController = new PublicController();
$bitacoraController = new BitacoraController();
$apiController = new ApiController();

switch ($route) {

    case 'login':
        $authController->showLogin();
        break;

    case 'auth/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController->login();
        }
        break;

    case 'logout':
        $authController->logout();
        break;

    case 'productos':
        $productoController->index();
        break;

    case 'productos/create':
        $productoController->create();
        break;

    case 'productos/store':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->store();
        }
        break;

    case 'productos/edit':
        $productoController->edit();
        break;

    case 'productos/update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->update();
        }
        break;

    case 'productos/delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productoController->delete();
        }
        break;

    case 'bitacora':
        $bitacoraController->index();
        break;

    case 'api/productos':
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'GET' && isset($_GET['id'])) {
            $apiController->show();
        } elseif ($method === 'GET') {
            $apiController->index();
        } elseif ($method === 'POST') {
            $apiController->store();
        } elseif ($method === 'PUT') {
            $apiController->update();
        } elseif ($method === 'DELETE') {
            $apiController->delete();
        }
        break;

    case 'catalogo':
    default:
        $publicController->catalogo();
        break;
}