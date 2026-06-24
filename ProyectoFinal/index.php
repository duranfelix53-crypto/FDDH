<?php

/**
 * Front controller — enruta peticiones según el parámetro `route`.
 *
 * Extrae parámetros de URLs limpias (/pagina/N, /buscar/TERM, /edit/N)
 * y los asigna a $_GET antes de despachar al controlador correspondiente.
 */

require_once __DIR__ . '/config/Autoload.php';

$route = $_GET['route'] ?? 'catalogo';

if (preg_match('#^(.+)/pagina/(\d+)$#', $route, $m)) {
    $_GET['pagina'] = (int) $m[2];
    $route = $m[1];
}

if (preg_match('#^(.+)/buscar/([^/]+)$#', $route, $m)) {
    $_GET['buscar'] = urldecode($m[2]);
    $route = $m[1];
}

if (preg_match('#^(.+/edit)/(\d+)$#', $route, $m)) {
    $_GET['id'] = (int) $m[2];
    $route = $m[1];
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case 'login':
        (new Controllers\AuthController())->showLogin();
        break;

    case 'auth/login':
        if ($method === 'POST') {
            (new Controllers\AuthController())->login();
        }
        break;

    case 'logout':
        (new Controllers\AuthController())->logout();
        break;

    case 'productos':
        (new Controllers\ProductoController())->index();
        break;

    case 'productos/create':
        (new Controllers\ProductoController())->create();
        break;

    case 'productos/store':
        if ($method === 'POST') {
            (new Controllers\ProductoController())->store();
        }
        break;

    case 'productos/edit':
        (new Controllers\ProductoController())->edit();
        break;

    case 'productos/update':
        if ($method === 'POST') {
            (new Controllers\ProductoController())->update();
        }
        break;

    case 'productos/delete':
        if ($method === 'POST') {
            (new Controllers\ProductoController())->delete();
        }
        break;

    case 'productos/logs':
        (new Controllers\ProductoController())->logs();
        break;

    case 'api/productos':
        (new Controllers\ApiController())->productos();
        break;

    case 'catalogo':
    default:
        (new Controllers\PublicController())->catalogo();
        break;
}