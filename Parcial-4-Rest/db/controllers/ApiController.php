<?php
namespace Controllers;

use Models\ProductoModel;



class ApiController
{
    private ProductoModel $ProductoModel;

    public function __construct()
    {
        $this->ProductoModel = new ProductoModel();
    }

    private function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // GET /api/productos
    public function index(): void
    {
        $productos = $this->ProductoModel->obtenerTodos();
        $this->json(['success' => true, 'data' => $productos]);
    }

    // GET /api/productos?id=1
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ID inválido.'], 400);
        }
        $producto = $this->ProductoModel->buscarPorId($id);
        if (!$producto) {
            $this->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
        }
        $this->json(['success' => true, 'data' => $producto]);
    }

    // POST /api/productos
    public function store(): void
    {
        $body = json_decode(file_get_contents('php://input'), true);

        $data = [
            'sku'           => trim($body['sku'] ?? ''),
            'nombre'        => trim($body['nombre'] ?? ''),
            'descripcion'   => trim($body['descripcion'] ?? ''),
            'precio_compra' => floatval($body['precio_compra'] ?? 0),
            'precio_venta'  => floatval($body['precio_venta'] ?? 0),
            'existencia'    => intval($body['existencia'] ?? 0),
            'imagen'        => null
        ];

        if ($data['sku'] === '' || $data['nombre'] === '' || $data['descripcion'] === '') {
            $this->json(['success' => false, 'message' => 'Todos los campos son obligatorios.'], 400);
        }

        if ($data['precio_compra'] < 0 || $data['precio_venta'] < 0 || $data['existencia'] < 0) {
            $this->json(['success' => false, 'message' => 'No se permiten valores negativos.'], 400);
        }

        if ($data['precio_venta'] < $data['precio_compra']) {
            $this->json(['success' => false, 'message' => 'El precio de venta no puede ser menor al precio de compra.'], 400);
        }

        if ($this->ProductoModel->existeSku($data['sku'])) {
            $this->json(['success' => false, 'message' => 'El SKU ya está registrado.'], 409);
        }

        if ($this->ProductoModel->create($data)) {
            $this->json(['success' => true, 'message' => 'Producto creado correctamente.'], 201);
        } else {
            $this->json(['success' => false, 'message' => 'No fue posible crear el producto.'], 500);
        }
    }

    // PUT /api/productos?id=1
    public function update(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ID inválido.'], 400);
        }

        $body = json_decode(file_get_contents('php://input'), true);

        $data = [
            'sku'           => trim($body['sku'] ?? ''),
            'nombre'        => trim($body['nombre'] ?? ''),
            'descripcion'   => trim($body['descripcion'] ?? ''),
            'precio_compra' => floatval($body['precio_compra'] ?? 0),
            'precio_venta'  => floatval($body['precio_venta'] ?? 0),
            'existencia'    => intval($body['existencia'] ?? 0),
            'imagen'        => null
        ];

        if ($data['sku'] === '' || $data['nombre'] === '' || $data['descripcion'] === '') {
            $this->json(['success' => false, 'message' => 'Todos los campos son obligatorios.'], 400);
        }

        if ($data['precio_compra'] < 0 || $data['precio_venta'] < 0 || $data['existencia'] < 0) {
            $this->json(['success' => false, 'message' => 'No se permiten valores negativos.'], 400);
        }

        if ($data['precio_venta'] < $data['precio_compra']) {
            $this->json(['success' => false, 'message' => 'El precio de venta no puede ser menor al precio de compra.'], 400);
        }

        if ($this->ProductoModel->existeSku($data['sku'], $id)) {
            $this->json(['success' => false, 'message' => 'El SKU ya está registrado por otro producto.'], 409);
        }

        $productoActual = $this->ProductoModel->buscarPorId($id);
        $data['imagen'] = $productoActual['imagen'] ?? null;

        if ($this->ProductoModel->actualizar($id, $data)) {
            $this->json(['success' => true, 'message' => 'Producto actualizado correctamente.']);
        } else {
            $this->json(['success' => false, 'message' => 'No fue posible actualizar el producto.'], 500);
        }
    }

    // DELETE /api/productos?id=1
    public function delete(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'ID inválido.'], 400);
        }

        if ($this->ProductoModel->eliminar($id)) {
            $this->json(['success' => true, 'message' => 'Producto eliminado correctamente.']);
        } else {
            $this->json(['success' => false, 'message' => 'No fue posible eliminar el producto.'], 500);
        }
    }
}
?>