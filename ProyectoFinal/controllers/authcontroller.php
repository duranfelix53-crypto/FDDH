<?php
/* La clase AuthController en el espacio de nombres (namespace) Controllers 
se encarga de la autenticación de usuarios, incluyendo el inicio de sesión (login), 
el cierre de sesión (logout) y la gestión de sesiones. */
namespace Controllers;

use Models\UsuarioModel;
use Models\BitacoraModel;

class AuthController {
    public function showLogin() : void {
        require_once __DIR__ . "/../config/views/auth/login.php";
    }

    public function login() : void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $username = trim($_POST["username"] ?? "");
        $password = trim($_POST["password"] ?? "");

        if ($username === "" || $password === "") {
            $_SESSION["error"] = "Todos los campos son obligatorios";
            header("Location: index.php?route=login");
            exit;
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->buscarPorUsername($username);

        if ($usuario && password_verify($password, $usuario["password"])) {
            $_SESSION["admin"] = [
                "id"       => $usuario["id"],
                "username" => $usuario["username"],
                "nombre"   => $usuario["nombre"] // ← corregido
            ];

            $bitacora = new BitacoraModel();
            $bitacora->registrar($usuario["username"], "LOGIN", "Inicio de sesión exitoso");

            $_SESSION["success"] = "Bienvenido, " . $usuario["nombre"] . ".";
            header("Location: index.php?route=productos");
            exit;
        }

        $_SESSION["error"] = "Credenciales incorrectas";
        header("Location: index.php?route=login");
        exit;
    }

    public function logout() : void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $usuario = $_SESSION["admin"]["username"] ?? "desconocido";
        $bitacora = new BitacoraModel();
        $bitacora->registrar($usuario, "LOGOUT", "Sesión cerrada");
        session_destroy();
        header("Location: index.php?route=login");
        exit;
    }
}