<?php
/* The `ProductoController` class in PHP handles CRUD operations for products, including pagination,
creation, updating, and deletion, with session verification and logging functionality. */
namespace Controllers;

use Models\ProductoModel;
use Models\LogModel;
class ProductoController
{
    private ProductoModel $productoModel;
    private LogModel $logModel;

    public function __construct()
    {
        $this->productoModel = new ProductoModel();
        $this->logModel = new LogModel();
    }

    private function verificarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=login');
            exit;
        }
    }
// Paginación de productos
    public function index(): void
    {
        $this->verificarSesion();
        $pagina = (int)($_GET['pagina'] ?? 1);

        if ($pagina < 1){
            $pagina = 1;
        }
        $limite = 5;
        $offset = ($pagina - 1) * $limite;
        $productos = $this->productoModel->obtenerPaginados($limite, $offset);
        $totalProductos = $this->productoModel->contarProductos();
        $totalPaginas = ceil($totalProductos / $limite);
        // Forzamos a que si obtenerTodos() falla, sea un array vacío 
        require_once __DIR__ . '/../views/productos/index.php';
    }

    public function create(): void
    {
        $this->verificarSesion();
        require_once __DIR__ . '/../views/productos/create.php';
    }

    public function store(): void
    {
        $this->verificarSesion();

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? '')
        ];

        $nombreImagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            $extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $nombreImagen = uniqid() . '.' . $extension;
            $rutaDestino = __DIR__ . '/../views/img/' . $nombreImagen;
            move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino);
        }
        $data['imagen'] = $nombreImagen;

        if (
            $data['sku'] === '' ||
            $data['nombre'] === '' ||
            $data['descripcion'] === '' ||
            $data['precio_compra'] === ''||
            $data['precio_venta'] === ''||
            $data['existencia'] === ''

        ){
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        if (!is_numeric($data['precio_compra']) || !is_numeric($data['precio_venta'])
             || !is_numeric($data['existencia'])) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia deben ser numéricos.';
            header('Location: index.php?route=productos/create');
            exit;
        }
// Validar precio_venta ≥ precio_compra 
        if ((float)$data['precio_compra'] < 0 || (float)$data['precio_venta'] < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: index.php?route=productos/create');
            exit;
        }
// Validar existencia ≥ 0       
        if ((int)$data['existencia'] < 0) {
            $_SESSION['error'] = 'La existencia no puede ser negativa.';
            header('Location: index.php?route=productos/create');
            exit;
        }

        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor al precio de compra.';
            header('Location: index.php?route=productos/create');
            exit;
        }
// Mensaje si SKU ya existe (duplicado) log
       $resultado = $this->productoModel->crear($data);
       if ($resultado === "duplicado") {
        $_SESSION['error'] = 'El SKU ya existe.';
        } elseif ($resultado) {
            $_SESSION['success'] = 'Producto registrado correctamente.';
             $this->logModel->registrar($_SESSION['admin']['username'],'Registró el producto: ' . $data['nombre']);
        } else {
            $_SESSION['error'] = 'No fue posible registrar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }
    public function edit(): void
    {
        $this->verificarSesion();

        $id = (int)($_GET['id'] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: index.php?route=productos');
            exit;
        }

        require_once __DIR__ . '/../views/productos/edit.php';
    }

    public function update(): void
    {
        $this->verificarSesion();

        $id = (int)($_POST['id'] ?? 0);

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? '')
        ];

        $productoActual = $this->productoModel->obtenerPorId($id);
        $nombreImagen = $productoActual['imagen'] ?? null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === 0) {
            if (!empty($nombreImagen)) {
                $rutaVieja = __DIR__ . '/../views/img/' . $nombreImagen;
                if (file_exists($rutaVieja)) { unlink($rutaVieja);}
        }
        $extension = pathinfo(
        $_FILES['imagen']['name'], PATHINFO_EXTENSION);
        $nombreImagen = uniqid() . '.' . $extension;
        $rutaDestino = __DIR__ . '/../views/img/' . $nombreImagen;
        move_uploaded_file(
        $_FILES['imagen']['tmp_name'],
        $rutaDestino);
}

$data['imagen'] = $nombreImagen;

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            header('Location: index.php?route=productos');
            exit;
        }

        if (
            $data['sku'] === '' ||
            $data['nombre'] === '' ||
            $data['descripcion'] === '' ||
            $data['precio_compra'] === '' ||
            $data['precio_venta'] === '' ||
            $data['existencia'] === ''
        ) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        if (!is_numeric($data['precio_compra']) || !is_numeric($data['precio_venta'])
            || !is_numeric($data['existencia'])) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia 
             deben ser numéricos.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }
// Validar precio_venta ≥ precio_compra
        if ((float)$data['precio_compra'] < 0 || (float)$data['precio_venta'] 
             < 0) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }
// Validar existencia ≥ 0      
        if ((int)$data['existencia'] < 0) {
            $_SESSION['error'] = 'La existencia no puede ser negativa.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }
        
        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor al precio de compra.';
            header('Location: index.php?route=productos/edit&id=' . $id);
            exit;
        }

        if ($this->productoModel->actualizar($id, $data)) {
            $_SESSION['success'] = 'Producto actualizado correctamente.';
            $this->logModel->registrar($_SESSION['admin']['username'],'Actualizó el producto: ' . $data['nombre']);
        } else {
            $_SESSION['error'] = 'No fue posible actualizar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }

    public function delete(): void
    {
        $this->verificarSesion();

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID inválido.';
            header('Location: index.php?route=productos');
            exit;
        }
        $producto = $this->productoModel->obtenerPorId($id);
        if ($this->productoModel->eliminar($id)) {
            $_SESSION['success'] = 'Producto eliminado correctamente.';
            $this->logModel->registrar($_SESSION['admin']['username'],'Eliminó el producto: ' . $producto['nombre']);
        } else {
            $_SESSION['error'] = 'No fue posible eliminar el producto.';
        }

        header('Location: index.php?route=productos');
        exit;
    }
 
}
?>