<?php
namespace Controllers;

use Models\UsuarioModel;

class AuthController
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Inicia la sesión si no está iniciada
     */
    private function iniciarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Verifica si el usuario está autenticado
     */
    private function verificarAutenticacion(): bool
    {
        return isset($_SESSION['admin']);
    }

    /**
     * Muestra la página de login
     */
    public function showLogin(): void
    {
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Procesa el login del usuario
     */
    public function login(): void
    {
        $this->iniciarSesion();
         if (!isset($_POST['csrf_token']) || 
        $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Token de seguridad inválido.';
        header('Location: index.php?route=login');
        exit;
    }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Validar que los campos no estén vacíos
        if ($username === '' || $password === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=login');
            exit;
        }

        // Buscar usuario en la base de datos (solo por username)
        $usuario = $this->usuarioModel->buscarPorUsername($username);

        // Verificar que el usuario existe y la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['password'])) {
            // Login exitoso: guardar datos en la sesión
            $_SESSION['admin'] = [
                'id' => $usuario['id'],
                'username' => $usuario['username'],
                'home_re_complete' => $usuario['home_re_complete']
            ];
            $_SESSION['success'] = 'Bienvenido, ' . $usuario['home_re_complete'];
            header('Location: index.php?route=productos');
            exit;
        } else {
            // Login fallido
            $_SESSION['error'] = 'Credenciales incorrectas.';
            header('Location: index.php?route=login');
            exit;
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(): void
    {
        $this->iniciarSesion();
        session_destroy();
        header('Location: index.php?route=login');
        exit;
    }

    /**
     * Muestra el dashboard (página principal después de login)
     */
    public function index(): void
    {
        $this->iniciarSesion();

        // Verificar que el usuario está autenticado
        if (!$this->verificarAutenticacion()) {
            header('Location: index.php?route=login');
            exit;
        }

        require_once __DIR__ . '/../views/dashboard/index.php';
    }
}
?>