<?php

/**
 * Utilidades de seguridad para protección CSRF en formularios.
 *
 * Genera tokens aleatorios, los valida con comparación segura contra timing attacks
 * y provee métodos helper para formularios HTML.
 */

namespace Helpers;

/**
 * Utilidades de seguridad CSRF para formularios.
 */
class Security
{
    /**
     * Genera (o retorna existente) un token CSRF almacenado en sesión.
     *
     * @return string Token CSRF en hexadecimal
     */
    public static function generarTokenCSRF(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Retorna un campo `<input type="hidden">` con el token CSRF escapado.
     *
     * `htmlspecialchars()` previene XSS en el valor del token dentro del HTML.
     *
     * @return string HTML del campo oculto con el token
     */
    public static function campoCSRF(): string
    {
        $token = self::generarTokenCSRF();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Valida que el token CSRF enviado coincida con el de sesión.
     *
     * Utiliza `hash_equals()` para prevenir timing attacks en la comparación.
     *
     * @return bool `true` si el token es válido; `false` en caso contrario
     */
    public static function validarCSRF(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($_SESSION['csrf_token']) ||
            empty($_POST['_csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['_csrf_token'])
        ) {
            return false;
        }

        unset($_SESSION['csrf_token']);
        return true;
    }

    /**
     * Redirige a la página previa con mensaje de error por CSRF inválido.
     *
     * Usa HTTP_REFERER como destino; si no está disponible,
     * redirige al login como fallback.
     *
     * @return void (termina la ejecución)
     */
    public static function csrfDenegado(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['error'] = 'Solicitud no valida (CSRF).';
        $destino = $_SERVER['HTTP_REFERER'] ?? '/PROYECTO_FINAL_DWA/login';
        header('Location: ' . $destino);
        exit;
    }
}