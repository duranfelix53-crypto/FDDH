<?php

/**
 * Modelo de datos para la tabla de productos.
 *
 * Encapsula todas las operaciones CRUD y consultas de búsqueda
 * sobre la tabla productos usando PDO con transacciones.
 */

namespace Models;

use Config\Database;

use PDO;
use PDOException;

/**
 * Operaciones CRUD y consultas de búsqueda para la tabla productos.
 */
class ProductoModel {
    /** @var PDO Conexión a la base de datos */
    private PDO $conexion;

    /**
     * Inicializa la conexión a la base de datos mediante Config\Database.
     *
     * @return void
     */
    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    /**
     * Obtiene todos los productos ordenados del más reciente al más antiguo.
     *
     * @return array Lista de productos
     */
    public function obtenerTodos() : array {
        try {
            $sql = 'SELECT * FROM productos ORDER BY id DESC';
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Obtiene un subconjunto paginado de productos.
     *
     * @param int $limit Registros por página
     * @param int $offset Desplazamiento inicial
     * @return array Lista de productos
     */
    public function obtenerPaginados(int $limit, int $offset): array
    {
        try {
            $sql = 'SELECT * FROM productos ORDER BY id DESC LIMIT :limit OFFSET :offset';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    /**
     * Cuenta el total de productos en la tabla.
     *
     * @return int Cantidad total de productos
     */
    public function contarTodos(): int
    {
        try {
            $sql = 'SELECT COUNT(*) FROM productos';
            $stmt = $this->conexion->query($sql);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Verifica si un SKU ya existe, opcionalmente excluyendo un ID de producto.
     *
     * @param string $sku SKU a verificar
     * @param int|null $excluirId ID del producto a excluir de la verificación
     * @return bool `true` si el SKU ya está en uso
     */
    public function existeSku(string $sku, ?int $excluirId = null): bool
    {
        try {
            if ($excluirId !== null) {
                $sql = 'SELECT COUNT(*) FROM productos WHERE sku = :sku AND id != :id';
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':sku', $sku);
                $stmt->bindParam(':id', $excluirId, PDO::PARAM_INT);
            } else {
                $sql = 'SELECT COUNT(*) FROM productos WHERE sku = :sku';
                $stmt = $this->conexion->prepare($sql);
                $stmt->bindParam(':sku', $sku);
            }
            $stmt->execute();
            return (int) $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

/**
 * Busca productos por nombre o descripción (catálogo público).
 *
 * @param string $termino Término de búsqueda
 * @return array Productos coincidentes, o todos si el término está vacío
 */
public function buscarPublico(string $termino = '') : array {
    try {
        if (trim($termino) == ''){
            return $this->obtenerTodos();
        }

        $sql = 'SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino ORDER BY id DESC';
        $stmt = $this->conexion->prepare($sql);
        $busqueda = '%' . $termino . '%';
        $stmt->bindParam(':termino', $busqueda);
        $stmt->execute();
        return $stmt->fetchAll();

    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Busca productos por nombre o descripción con paginación.
 *
 * @param string $termino Término de búsqueda
 * @param int $limit Registros por página
 * @param int $offset Desplazamiento inicial
 * @return array Productos coincidentes
 */
public function buscarPublicoPaginados(string $termino, int $limit, int $offset): array
{
    try {
        if (trim($termino) === '') {
            return $this->obtenerPaginados($limit, $offset);
        }

        $sql = 'SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino ORDER BY id DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->conexion->prepare($sql);
        $busqueda = '%' . $termino . '%';
        $stmt->bindParam(':termino', $busqueda);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Cuenta los productos que coinciden con un término de búsqueda.
 *
 * @param string $termino Término de búsqueda
 * @return int Cantidad de productos coincidentes
 */
public function contarBusqueda(string $termino): int
{
    try {
        if (trim($termino) === '') {
            return $this->contarTodos();
        }

        $sql = 'SELECT COUNT(*) FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino';
        $stmt = $this->conexion->prepare($sql);
        $busqueda = '%' . $termino . '%';
        $stmt->bindParam(':termino', $busqueda);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

/**
 * Obtiene un producto por su ID.
 *
 * @param int $id ID del producto
 * @return array|null Datos del producto o null si no existe
 */
public function obtenerPorId(int $id) : ?array {
    try {

        $sql = 'SELECT * FROM productos WHERE id = :id LIMIT 1';
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $producto = $stmt->fetch();
        return $producto ?: null;
    }   catch (PDOException $e) {
        return null;
    }
}

/**
 * Inserta un nuevo producto usando una transacción para garantizar integridad de datos.
 *
 * @param array $data Arreglo asociativo con claves: sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen
 * @return bool `true` si se insertó correctamente
 */
public function crear(array $data): bool
{
    try {

    $this->conexion->beginTransaction();

    $sql = 'INSERT INTO productos (sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen) VALUES (:sku, :nombre, :descripcion, :precio_compra, :precio_venta, :existencia, :imagen)';
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindParam(':sku', $data['sku']);
    $stmt->bindParam(':nombre', $data['nombre']);
    $stmt->bindParam(':descripcion', $data['descripcion']);
    $stmt->bindParam(':precio_compra', $data['precio_compra']);
    $stmt->bindParam(':precio_venta', $data['precio_venta']);
    $stmt->bindParam(':existencia', $data['existencia'], PDO::PARAM_INT);
    $stmt->bindParam(':imagen', $data['imagen']);

    $resultado = $stmt->execute();
     if (!$resultado) {
            $this->conexion->rollBack();
            return false;
        }
     $this->conexion->commit();
    return true;

    } catch(PDOException $e) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
        return false;
    }


}

/**
 * Actualiza un producto existente dentro de una transacción para garantizar atomicidad.
 *
 * Si alguna operación falla, se revierte automáticamente para evitar estados inconsistentes.
 *
 * @param int $id ID del producto a actualizar
 * @param array $data Arreglo asociativo con claves: sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen
 * @return bool `true` si se actualizó correctamente
 */
public function actualizar(int $id, array $data): bool
{
    try{
        $this->conexion->beginTransaction();

        $sql = 'UPDATE productos SET
                sku = :sku,
                nombre = :nombre,
                descripcion = :descripcion,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                existencia = :existencia,
                imagen = :imagen
                WHERE id = :id';

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(':sku', $data['sku']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':descripcion', $data['descripcion']);
        $stmt->bindParam(':precio_compra', $data['precio_compra']);
        $stmt->bindParam(':precio_venta', $data['precio_venta']);
        $stmt->bindParam(':existencia', $data['existencia'], PDO::PARAM_INT);
        $stmt->bindParam(':imagen', $data['imagen']);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

         $resultado = $stmt->execute();
         if (!$resultado) {
             $this->conexion->rollBack();
             return false;
         }

         $this->conexion->commit();
         return true;
         } catch (PDOException $e) {
         if ($this->conexion->inTransaction()) {
             $this->conexion->rollBack();
         }
         return false;

    }

}

    /**
     * Elimina un producto junto con su archivo de imagen asociado, usando una transacción.
     *
     * La eliminación del registro y del archivo se tratan de forma atómica:
     * si el archivo existe y no se puede borrar, se revierte la transacción completa.
     *
     * @param int $id ID del producto a eliminar
     * @return bool `true` si se eliminó correctamente
     */
    public function eliminar(int $id): bool
    {
        try {
            $producto = $this->obtenerPorId($id);

            $this->conexion->beginTransaction();

            $sql = 'DELETE FROM productos WHERE id = :id';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
                $this->conexion->rollBack();
                return false;
            }

            if ($producto && !empty($producto['imagen'])) {
                $ruta = __DIR__ . '/../views/img/productos/' . $producto['imagen'];
                if (file_exists($ruta) && !unlink($ruta)) {
                    $this->conexion->rollBack();
                    return false;
                }
            }

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

}