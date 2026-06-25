<?php
namespace Controllers;

use Models\ProductoModel;
use Models\BitacoraModel;

class ProductoController {
    private ProductoModel $ProductoModel;
    private BitacoraModel $BitacoraModel;

    public function __construct() {
        $this->ProductoModel = new ProductoModel();
        $this->BitacoraModel = new BitacoraModel();
    }

    private function verificarSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=login');
            exit;
        }
    }

    private function validarCSRF(): void {
        if (!isset($_POST['csrf_token']) || 
            $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Token de seguridad inválido.';
            header('Location: index.php?route=productos');
            exit;
        }
    }

    private function procesarImagen(): ?string {
        if (!isset($_FILES['imagen']) || $_FILES['imagen']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $extensionesPermitidas)) {
            return null;
        }

        $carpeta = __DIR__ . '/../uploads/productos/';
        if (!is_dir($carpeta)) {
            mkdir($carpeta, 0755, true);
        }

        $nombreArchivo = uniqid('prod_') . '.' . $ext;
        move_uploaded_file($_FILES['imagen']['tmp_name'], $carpeta . $nombreArchivo);

        return $nombreArchivo;
    }

    public function index(): void {
        $this->verificarSesion();

        $porPagina = 5;
        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $total = $this->ProductoModel->contarProductos();
        $totalPaginas = (int)ceil($total / $porPagina);

        $productos = $this->ProductoModel->obtenerTodos($pagina, $porPagina);

        require_once __DIR__ . '/../views/productos/index.php';
    }

    public function create(): void {
        $this->verificarSesion();
        require_once __DIR__ . '/../views/productos/create.php';
    }

    public function store(): void {
        $this->verificarSesion();
        $this->validarCSRF();

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => floatval($_POST['precio_compra'] ?? 0),
            'precio_venta' => floatval($_POST['precio_venta'] ?? 0),
            'existencia' => intval($_POST['existencia'] ?? 0)
        ];

        if ($data['sku'] === '' || $data['nombre'] === '' || $data['descripcion'] === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        if (!is_numeric($_POST['precio_compra']) || !is_numeric($_POST['precio_venta'])
            || !is_numeric($_POST['existencia'])) {
            $_SESSION['error'] = 'Precio compra, precio venta y existencia deben ser numéricos.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        if ((float)$data['precio_compra'] < 0 || (float)$data['precio_venta'] < 0
            || (int)$data['existencia'] < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor al precio de compra.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        // Validar SKU único
        if ($this->ProductoModel->existeSku($data['sku'])) {
            $_SESSION['error'] = 'El SKU ya está registrado.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        $data['imagen'] = $this->procesarImagen();

        if ($this->ProductoModel->create($data)) {
            $this->BitacoraModel->registrar(
                $_SESSION['admin']['id'],
                $_SESSION['admin']['username'],   // ← cambiado home_re_complete
                'Registró producto: ' . $data['nombre']
            );
            $_SESSION['success'] = 'Producto registrado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible registrar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }

    public function edit(): void {
        $this->verificarSesion();
        $id = (int)($_GET['id'] ?? 0);
        $producto = $this->ProductoModel->buscarPorId($id);
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: index.php?route=productos');
            exit;
        }
        require_once __DIR__ . '/../views/productos/edit.php';
    }

    public function update(): void {
        $this->verificarSesion();
        $this->validarCSRF();

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? '')
        ];

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            header('Location: index.php?route=productos');
            exit;
        }

        if ($data['sku'] === '' || $data['nombre'] === '' || $data['descripcion'] === '') {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        if (!is_numeric($data['precio_compra']) || !is_numeric($data['precio_venta'])
            || !is_numeric($data['existencia'])) {
            $_SESSION['error'] = 'Precio compra, precio venta y existencia deben ser numéricos.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        if ((float)$data['precio_compra'] < 0 || (float)$data['precio_venta'] < 0
            || (int)$data['existencia'] < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor al precio de compra.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        // Validar SKU único excepto el producto actual
        if ($this->ProductoModel->existeSku($data['sku'], $id)) {
            $_SESSION['error'] = 'El SKU ya está registrado por otro producto.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        $imagenNueva = $this->procesarImagen();
        if ($imagenNueva !== null) {
            $data['imagen'] = $imagenNueva;
        } else {
            $productoActual = $this->ProductoModel->buscarPorId($id);
            $data['imagen'] = $productoActual['imagen'] ?? null;
        }

        if ($this->ProductoModel->actualizar($id, $data)) {
            $this->BitacoraModel->registrar(
                $_SESSION['admin']['id'],
                $_SESSION['admin']['username'],   // ← cambiado home_re_complete
                'Actualizó producto: ' . $data['nombre']
            );
            $_SESSION['success'] = 'Producto actualizado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible actualizar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }

    public function delete(): void {
        $this->verificarSesion();
        $this->validarCSRF();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido';
            header('Location: index.php?route=productos');
            exit;
        }

        if ($this->ProductoModel->eliminar($id)) {
            $this->BitacoraModel->registrar(
                $_SESSION['admin']['id'],
                $_SESSION['admin']['username'],   // ← cambiado home_re_complete
                'Eliminó producto ID: ' . $id
            );
            $_SESSION['success'] = 'Producto eliminado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible eliminar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }
}
?>