<?php
namespace Controllers;
use Models\ProductoModel;
use Models\BitacoraModel;

class ProductoController {
    private ProductoModel $productoModel;
    private BitacoraModel $bitacoraModel;

    public function __construct() {
        $this->productoModel = new ProductoModel();
        $this->bitacoraModel = new BitacoraModel();
    }

    private function verificarSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION["admin"])) {
            header("Location: index.php?route=login");
            exit;
        }
    }

    public function index(): void {
        $this->verificarSesion();
        $productos = $this->productoModel->obtenerTodos();
        $productos = $productos ?: [];
        $this->verificarSesion();

        $porPagina = 10;
        $paginaActual = (int)($_GET["pagina"] ?? 1);
        if ($paginaActual < 1) $paginaActual = 1;

        $total = $this->productoModel->contarTodos();
        $totalPaginas = (int)ceil($total / $porPagina);
        if ($paginaActual > $totalPaginas && $totalPaginas > 0) $paginaActual = $totalPaginas;

        $offset = ($paginaActual - 1) * $porPagina;
        $productos = $this->productoModel->obtenerPorPagina($porPagina, $offset);
        $productos = $productos ?: [];

        require_once __DIR__ . "/../config/views/productos/index.php";
    }

    public function create(): void {
        $this->verificarSesion();
        require_once __DIR__ . "/../config/views/productos/create.php";
    }

   
    public function store(): void {
    $this->verificarSesion();
    $data = [
        "sku"           => trim($_POST["sku"] ?? ""),
        "nombre"        => trim($_POST["nombre"] ?? ""),
        "descripcion"   => trim($_POST["descripcion"] ?? ""),
        "precio_compra" => trim($_POST["precio_compra"] ?? ""),
        "precio_venta"  => trim($_POST["precio_venta"] ?? ""),
        "existencia"    => trim($_POST["existencia"] ?? "")
    ];

    if (in_array("", $data, true)) {
        $_SESSION["error"] = "Todos los campos son obligatorios";
        header("Location: index.php?route=productos/create");
        exit;
    }

    if (!is_numeric($data["precio_compra"]) || !is_numeric($data["precio_venta"]) || !is_numeric($data["existencia"])) {
        $_SESSION["error"] = "Precio de compra, precio de venta y existencia deben ser numéricos.";
        header("Location: index.php?route=productos/create");
        exit;
    }

    if ((float)$data["precio_compra"] < 0 || (float)$data["precio_venta"] < 0 || (int)$data["existencia"] < 0) {
        $_SESSION["error"] = "No se permiten valores negativos";
        header("Location: index.php?route=productos/create");
        exit;
    }

    if ((float)$data["precio_venta"] < (float)$data["precio_compra"]) {
        $_SESSION["error"] = "El precio de venta no puede ser menor al precio de compra.";
        header("Location: index.php?route=productos/create");
        exit;
    }

    if ($this->productoModel->skuExiste($data["sku"])) {
        $_SESSION["error"] = "El SKU ya existe, ingresa uno diferente.";
        header("Location: index.php?route=productos/create");
        exit;
    }

    // Manejo de imagen
    $data["imagen"] = null;
    if (isset($_FILES["imagen"]) && $_FILES["imagen"]["error"] === UPLOAD_ERR_OK) {
        $extension = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid("img_") . "." . $extension;
        $destino = __DIR__ . "/../IMG/" . $nombreArchivo;

        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $destino)) {
            $data["imagen"] = $nombreArchivo;
        }
    }

    if ($this->productoModel->crear($data)) {
        $usuario = $_SESSION["admin"]["username"];
        $this->bitacoraModel->registrar($usuario, "CREAR", "Producto creado: " . $data["nombre"] . " SKU: " . $data["sku"]);
        $_SESSION["success"] = "Producto registrado correctamente.";
    } else {
        $_SESSION["error"] = "No fue posible registrar el producto.";
    }

    header("Location: index.php?route=productos");
    exit;
}


    public function edit(): void {
        $this->verificarSesion();
        $id = (int)($_GET["id"] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);
        if (!$producto) {
            $_SESSION["error"] = "Producto no encontrado";
            header("Location: index.php?route=productos");
            exit;
        }
        require_once __DIR__ . "/../config/views/productos/edit.php";
    }

    public function update(): void {
        $this->verificarSesion();
        $id = (int)($_POST["id"] ?? 0);
        $data = [
            "sku"           => trim($_POST["sku"] ?? ""),
            "nombre"        => trim($_POST["nombre"] ?? ""),
            "descripcion"   => trim($_POST["descripcion"] ?? ""),
            "precio_compra" => trim($_POST["precio_compra"] ?? ""),
            "precio_venta"  => trim($_POST["precio_venta"] ?? ""),
            "existencia"    => trim($_POST["existencia"] ?? "")
        ];

        if (in_array("", $data, true)) {
            $_SESSION["error"] = "Todos los campos son obligatorios";
            header("Location: index.php?route=productos/edit&id=$id");
            exit;
        }

        if ($this->productoModel->skuExiste($data["sku"], $id)) {
            $_SESSION["error"] = "El SKU ya existe, ingresa uno diferente.";
            header("Location: index.php?route=productos/edit&id=$id");
            exit;
        }

        if ($this->productoModel->actualizar($id, $data)) {
            $_SESSION["success"] = "Producto actualizado correctamente.";
        } else {
            $_SESSION["error"] = "No fue posible actualizar el producto.";
        }

        if ($this->productoModel->actualizar($id, $data)) {
            $usuario = $_SESSION["admin"]["username"];
            $this->bitacoraModel->registrar($usuario, "EDITAR", "Producto editado ID: $id nombre: " . $data["nombre"]);
            $_SESSION["success"] = "Producto actualizado correctamente.";
        }

        header("Location: index.php?route=productos");
        exit;
    }

    public function delete(): void {
        $this->verificarSesion();
        $id = (int)($_POST["id"] ?? 0);

        if ($this->productoModel->eliminar($id)) {
            $_SESSION["success"] = "Producto eliminado correctamente.";
        } else {
            $_SESSION["error"] = "No fue posible eliminar el producto.";
        }
        if ($this->productoModel->eliminar($id)) {
            $usuario = $_SESSION["admin"]["username"];
            $this->bitacoraModel->registrar($usuario, "ELIMINAR", "Producto eliminado ID: $id");
            $_SESSION["success"] = "Producto eliminado correctamente.";
        }
        header("Location: index.php?route=productos");
        exit;
    }

    public function apiProductos(): void
    {
        header('Content-Type: application/json');

        $productoModel = new ProductoModel();
        $productos = $productoModel->obtenerTodos();

        echo json_encode($productos);

        exit;
    }

    public function bitacora(): void {
    $this->verificarSesion();
    $bitacoraModel = new BitacoraModel();
    $registros = $bitacoraModel->obtenerTodos();
    require_once __DIR__ . "/../config/views/productos/bitacora.php";
}

    
    
}