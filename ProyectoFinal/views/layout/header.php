<?php
/**
 * Cabecera HTML compartida y barra de navegación.
 *
 * Inicia la sesión, renderiza el <head> con Bootstrap/Font Awesome CDN,
 * variables CSS personalizadas, barra de navegación principal y
 * mensajes flash de sesión (éxito/error).
 */
?>

<?php if (session_status() === PHP_SESSION_NONE) session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <base href="/PROYECTO_FINAL_DWA/">
    <title>Desarrollo Web Avanzado: POO+PDO-TryCatch-Namespaces-Autoload-Transacciones-MVC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
    :root {
        --header-blue: #0067A5;
        --footer-blue: #002D58;
        --accent-blue: #8CC8FF;
        --accent-blue-light: #B8DEFF;
        --background: #F5F7FA;
        --text: #1F2937;
        --border: #D9E2EC;
    }

    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        background: var(--background);
        color: var(--text);
    }

    main {
        flex: 1;
    }

    .container {
        max-width: 1200px;
    }

    .navbar-custom {
        background: var(--header-blue);
        box-shadow: 0 2px 8px rgba(0,0,0,.12);
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 1.1rem;
    }

    .btn-primary {
        background: var(--header-blue);
        border-color: var(--header-blue);
    }

    .btn-primary:hover {
        background: var(--footer-blue);
        border-color: var(--footer-blue);
    }

    .btn-warning {
        background: var(--accent-blue);
        border-color: var(--accent-blue);
        color: var(--footer-blue);
        font-weight: 600;
    }

    .btn-warning:hover {
        background: var(--accent-blue-light);
        border-color: var(--accent-blue-light);
        color: var(--footer-blue);
    }

    .card {
        border: 1px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,.08);
        transition: .25s ease;
        background: #fff;
    }

    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 24px rgba(0,0,0,.14);
    }

    .card-img-top {
        height: 220px !important;
        object-fit: contain !important;
        padding: 15px;
        background: #fff;
    }

    .form-control {
        border-radius: 12px;
        border: 1px solid var(--border);
    }

    .form-control:focus {
        border-color: var(--header-blue);
        box-shadow: 0 0 0 .2rem rgba(0,103,165,.15);
    }

    footer {
        background: var(--footer-blue) !important;
    }

    footer .text-warning {
        color: var(--accent-blue) !important;
    }

    .bg-footer-bottom {
        background-color: var(--accent-blue-light);
    }

    .text-footer-dark {
        color: var(--footer-blue);
    }

    h2 {
        font-weight: 700;
        color: var(--text);
    }
    </style>

</head>

<body>
<main>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">

        <a class="navbar-brand d-flex align-items-center"
           href="catalogo">

            <i class="fa-solid fa-cart-shopping me-2" style="color: var(--accent-blue);"></i>

            <span>Tienda MVC</span>

        </a>

        <div>
            <a class="btn btn-outline-light btn-sm me-2"
               href="catalogo">
               Catalogo
            </a>

            <a class="btn btn-warning btn-sm"
               href="login">
               Administrador
            </a>
        </div>

    </div>
</nav>

<div class="container mt-4">

    <?php /** Muestra mensaje flash de éxito si existe, luego lo elimina. */ ?>
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <?php /** Muestra mensaje flash de error si existe, luego lo elimina. */ ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>