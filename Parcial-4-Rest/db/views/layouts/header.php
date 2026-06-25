<?php if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
} ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Desarrollo Web Avanzado: PDO+POO+TryCatch+Namespaces+Autoload+Transacciones+MVC</title>
    
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">

        <a class="navbar-brand" href="index.php?route=catalogo">
            Tienda MVC
        </a>

        <div>
            <a class="btn btn-outline-light btn-sm me-2"
               href="index.php?route=catalogo">
                Catálogo
            </a>

            <a class="btn btn-warning btn-sm"
               href="index.php?route=login">
                Administrador
            </a>
        </div>

    </div>
</nav>

<div class="container mt-4">

    <?php if (isset($_SESSION['success'])): ?>

        <div class="alert alert-success">

            <?= htmlspecialchars($_SESSION['success']); ?>

            <?php unset($_SESSION['success']); ?>

        </div>

    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_SESSION['error']); ?>

            <?php unset($_SESSION['error']); ?>

        </div>

    <?php endif; ?>

    

