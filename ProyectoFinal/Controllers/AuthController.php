<?php

/**
 * Controlador de autenticación de administradores.
 *
 * Gestiona el inicio de sesión, cierre de sesión y protección
 * de rutas administrativas mediante verificación de sesión.
 */

namespace Controllers;

use Models\UsuarioModel;
use Helpers\Security;
use Helpers\Logger;

/**
 * Controlador de autenticación — login, logout y vista de login.
 */
class AuthController
{
    /**
     * Muestra el formulario de login con token CSRF.
     *
     * @return void
     */
    public function showLogin(): void
    {
        Security::generarTokenCSRF();
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa el formulario de inicio de sesión.
     *
     * Valida CSRF, credenciales vacías, verifica usuario y contraseña con
     * `password_verify()` (compara el hash bcrypt sin revelar el hash original),
     * establece la sesión y redirige al panel o al login con error.
     *
     * @return void
     */
    public function login(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!Security::validarCSRF()) {
            Security::csrfDenegado();
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: /PROYECTO_FINAL_DWA/login');
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

            Logger::registrar('LOGIN', "Usuario: {$username}");

            $_SESSION['success'] = 'Bienvenido, ' . $usuario['nombre_completo'] . '.';
            header('Location: /PROYECTO_FINAL_DWA/productos');
            exit;
        }

        Logger::registrar('LOGIN_FALLIDO', "Intento fallido para usuario: {$username}");
        $_SESSION['error'] = 'Credenciales incorrectas.';
        header('Location: /PROYECTO_FINAL_DWA/login');
        exit;
    }

    /**
     * Cierra la sesión del usuario y redirige al login.
     *
     * @return void
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = $_SESSION['admin']['username'] ?? 'desconocido';
        Logger::registrar('LOGOUT', "Usuario: {$username}");

        session_destroy();
        header('Location: /PROYECTO_FINAL_DWA/login');
        exit;
    }
}