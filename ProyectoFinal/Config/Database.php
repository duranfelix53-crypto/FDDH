<?php

/**
 * Configuración y conexión a base de datos MySQL mediante PDO.
 *
 * Define credenciales de acceso y retorna una instancia de PDO
 * con manejo de errores por excepción.
 */

namespace Config;

use PDO;
use PDOException;

/**
 * Conexión a base de datos MySQL vía PDO.
 */
class Database
{
    /** @var string Servidor de base de datos */
    private string $host = 'localhost';

    /** @var string Nombre de la base de datos */
    private string $dbName = 'tienda_mvc';

    /** @var string Usuario de la base de datos */
    private string $username = 'root';

    /** @var string Contraseña de la base de datos */
    private string $password = '';

    /** @var string Charset de la conexión */
    private string $charset = 'utf8mb4';

    /**
     * Crea y retorna una conexión PDO configurada.
     *
     * - `ERRMODE_EXCEPTION`: lanza excepciones en errores SQL en lugar de advertir silenciosamente.
     * - `FETCH_ASSOC`: retorna resultados como arreglos asociativos por defecto, sin duplicar índices numéricos.
     *
     * @return PDO Instancia de conexión PDO
     * @throws PDOException Si la conexión falla (termina la ejecución)
     */
    public function connect(): PDO
    {
        try {

            $dsn = "mysql:host={$this->host};dbname={$this->dbName};charset={$this->charset}";

            $pdo = new PDO(
                $dsn,
                $this->username,
                $this->password
            );

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->setAttribute(
                PDO::ATTR_DEFAULT_FETCH_MODE,
                PDO::FETCH_ASSOC
            );

            return $pdo;

        } catch (PDOException $e) {

            die('Error de conexion: ' . $e->getMessage());
        }
    }
}