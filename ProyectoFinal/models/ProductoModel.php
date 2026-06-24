<?php
/* La clase ProductoModel en el espacio de nombres (namespace) Models 
proporciona métodos para interactuar con una tabla de base de datos 
que almacena información de productos, incluyendo la obtención de todos 
los productos, la búsqueda de productos, la creación, actualización y 
eliminación de productos, así como la verificación de si un SKU ya existe. */
namespace Models;

use Config\Database;
use PDO;
use PDOException;

class ProductoModel{
    private PDO $conexion;

    public function __construct() {
        $db = new Database();
        $this->conexion = $db->connect();
    }

    public function obtenerTodos() : array {
        try {
            $sql = "SELECT * FROM productos ORDER BY id DESC";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function buscarPublico(string $termino = "") : array {
        try {
            if (trim($termino) === "") {
                return $this->obtenerTodos();
            }

            $sql = "SELECT * FROM productos WHERE nombre LIKE :termino OR descripcion LIKE :termino ORDER BY id DESC";
            $stmt = $this->conexion->prepare($sql);
            $busqueda = "%". $termino. "%";
            $stmt->bindParam(":termino", $busqueda);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function obtenerPorId( int $id) : ?array {
        try {
            $sql = "SELECT * FROM productos WHERE id = :id LIMIT 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $producto = $stmt->fetch();
            return $producto ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }

public function crear(array $data) : bool {
    try {
        $this->conexion->beginTransaction();

        $sql = "INSERT INTO productos (sku, nombre, descripcion, precio_compra, precio_venta, existencia, imagen)
                VALUES (:sku, :nombre, :descripcion, :precio_compra, :precio_venta, :existencia, :imagen)";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":sku", $data["sku"]);
        $stmt->bindParam(":nombre", $data["nombre"]);
        $stmt->bindParam(":descripcion", $data["descripcion"]);
        $stmt->bindParam(":precio_compra", $data["precio_compra"]);
        $stmt->bindParam(":precio_venta", $data["precio_venta"]);
        $stmt->bindParam(":existencia", $data["existencia"], PDO::PARAM_INT);
        $stmt->bindParam(":imagen", $data["imagen"]);

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

public function actualizar(int $id, array $data) : bool {
    try {
        $this->conexion->beginTransaction();

        $sql = "UPDATE productos SET
                sku = :sku,
                nombre = :nombre,
                descripcion = :descripcion,
                precio_compra = :precio_compra,
                precio_venta = :precio_venta,
                existencia = :existencia
                WHERE id = :id";
            
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":sku", $data["sku"]);
        $stmt->bindParam(":nombre", $data["nombre"]);
        $stmt->bindParam(":descripcion", $data["descripcion"]);
        $stmt->bindParam(":precio_compra", $data["precio_compra"]);
        $stmt->bindParam(":precio_venta", $data["precio_venta"]);
        $stmt->bindParam(":existencia", $data["existencia"], PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        $this->conexion->commit();
        return true;
    } catch (PDOException) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
        return false;
    }
}

public function eliminar (int $id) : bool {
    try {
        $this->conexion->beginTransaction();
        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            $this->conexion->rollback();
            return false;
        }

        $this->conexion->commit();
        return true;

    } catch (PDOException) {
        if ($this->conexion->inTransaction()) {
            $this->conexion->rollBack();
        }
        return false;
    }
}

public function skuExiste(string $sku, int $excludeId = 0): bool {
    try {
        $sql = "SELECT COUNT(*) FROM productos WHERE sku = :sku AND id != :id";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":sku", $sku);
        $stmt->bindParam(":id", $excludeId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

public function contarTodos(): int {
    try {
        $stmt = $this->conexion->query("SELECT COUNT(*) FROM productos");
        return (int)$stmt->fetchColumn();
    } catch (PDOException $e) {
        return 0;
    }
}

public function obtenerPorPagina(int $limite, int $offset): array {
    try {
        $sql = "SELECT * FROM productos ORDER BY id DESC LIMIT :limite OFFSET :offset";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

}