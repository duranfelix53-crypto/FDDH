<?php
/* The AuthController class in PHP handles user authentication, including login, logout, and CSRF token
validation. */
namespace Controllers;

use Models\UsuarioModel;
use Models\LogModel;
//csrf
class AuthController
{
    private function validarCSRF(): void
    {
    if (!isset($_POST['csrf_token']) ||!hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token'])) {
                die('Token CSRF inválido.');
            }
}
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->validarCSRF();
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=login');
            exit;
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorUsername($username);

        if ($usuario && password_verify($password, $usuario['password'])) {
            $_SESSION['admin'] = [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'nombre_completo' => $usuario['nombre_completo']
            ];

            $_SESSION['success'] = 'Bienvenido, ' . $usuario['nombre_completo'];
            $logModel = new LogModel();
            $logModel->registrar($usuario['username'], 'Inició sesión');
            header('Location: index.php?route=productos');
            exit;
        }

        $_SESSION['error'] = 'Credenciales incorrectas.';
        header('Location: index.php?route=login');
        exit;
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }
}