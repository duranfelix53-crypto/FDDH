<?php
/* The `ProductoModel` class in the PHP namespace `Models` provides methods for interacting with a
database table storing product information, including pagination, searching, creating, updating, and
deleting products. */
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class ProductoModel
{
    private PDO $conexion;

    public function __construct()
    {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function obtenerTodos(): array
    {
        try {
            $sql = 'SELECT * FROM productos ORDER BY id DESC';
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC)?: [];
        } catch (PDOException $e) {
            return [];
        }
    }
// Paginación de productos
    public function obtenerPaginados(int $limite, int $offset): array
    {
        try {
            $sql = "
            SELECT *
            FROM productos
            ORDER BY id DESC
            LIMIT :limite OFFSET :offset
        ";
        
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            return [];
        }
    }

    public function contarProductos(): int
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM productos";
            $stmt = $this->conexion->query($sql);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$resultado['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function buscarPublico(string $termino = ''): array
    {
        try {
            if (trim($termino) === '') {
                return $this->obtenerTodos();
            }

            $sql = 'SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino ORDER BY id DESC';
            $stmt = $this->conexion->prepare($sql);
            $busqueda = '%' . $termino . '%';
            $stmt->bindParam(':termino', $busqueda);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerPorId(int $id): ?array
    {
        try {
            $sql = 'SELECT * FROM productos WHERE id = :id LIMIT 1';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch();
            return $producto ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public function crear(array $data)
    {
        try {
            $this->conexion->beginTransaction();
            // Mensaje si SKU ya existe (duplicado)
            $check = $this->conexion->prepare("
                SELECT id
                FROM productos
                WHERE sku = :sku
            ");

            $check->bindParam(':sku', $data['sku']);
            $check->execute();
            if ($check->rowCount() > 0) {
                return "duplicado";
            }
//agregue la imagen en la base de datos
            $sql = "INSERT INTO productos (sku, nombre, descripcion, precio_compra,
            precio_venta, existencia, imagen)
            VALUES (:sku, :nombre, :descripcion, :precio_compra, :precio_venta,
            :existencia, :imagen)";

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
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function actualizar(int $id, array $data): bool
    {
        try {
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
            $stmt->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            if ($this->conexion->inTransaction()) {
                $this->conexion->rollBack();
            }
            return false;
        }
    }

    public function eliminar(int $id): bool
    {
        try {
            $this->conexion->beginTransaction();
            $sql = 'DELETE FROM productos WHERE id = :id';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() === 0) {
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

}
?>