<?php

/**
 * Autoloader PSR-4-like.
 *
 * Convierte namespaces en rutas de archivo relativas a la raíz del proyecto.
 * El primer segmento del namespace se pasa a minúsculas.
 */

spl_autoload_register(function ($class) {

    $baseDir = __DIR__ . '/../';

    $class = str_replace('\\', '/', $class);

    $parts = explode('/', $class);

    if (!empty($parts)) {
        $parts[0] = strtolower($parts[0]);
    }

    $file = $baseDir . implode('/', $parts) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});