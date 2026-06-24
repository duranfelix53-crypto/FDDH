<?php

/**
 * Sistema de bitácora (log) para registrar acciones administrativas.
 *
 * Escribe líneas con marca de tiempo, usuario y acción en un archivo de texto,
 * usando bloqueo de escritura para evitar corrupción concurrente.
 */

namespace Helpers;

/**
 * Logger estático para registrar acciones administrativas en archivo.
 */
class Logger
{
    /** @var string Ruta del archivo de log */
    private static string $logFile = __DIR__ . '/../logs/admin.log';

    /**
     * Escribe una línea de log con marca de tiempo y usuario.
     *
     * Usa `FILE_APPEND | LOCK_EX` para evitar corrupción de datos por escrituras concurrentes.
     *
     * @param string $accion   Acción realizada (ej. LOGIN, CREAR, ELIMINAR)
     * @param string $detalles Información adicional opcional
     * @return void
     */
    public static function registrar(string $accion, string $detalles = ''): void
    {
        $fecha = date('Y-m-d H:i:s');

        $admin = 'anonimo';
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['admin']['username'])) {
            $admin = $_SESSION['admin']['username'];
        }

        $linea = "[{$fecha}] [{$admin}] {$accion}";
        if ($detalles !== '') {
            $linea .= " | {$detalles}";
        }
        $linea .= PHP_EOL;

        $dir = dirname(self::$logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents(self::$logFile, $linea, FILE_APPEND | LOCK_EX);
    }
}