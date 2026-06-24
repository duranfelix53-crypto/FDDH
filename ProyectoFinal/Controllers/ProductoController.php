<?php

/**
 * Controlador de administración de productos (CRUD completo).
 *
 * Gestiona la creación, edición, eliminación y visualización de productos,
 * incluyendo subida de imágenes con validación MIME, protección CSRF
 * y registro de acciones en la bitácora.
 */

namespace Controllers;

use Models\ProductoModel;
use Helpers\Security;
use Helpers\Logger;

/**
 * Controlador CRUD de productos con subida de imágenes y logs.
 */
class ProductoController
{
    /** @var ProductoModel Modelo de productos */
    private ProductoModel $productoModel;

    /**
     * Inicializa el modelo de productos.
     *
     * @return void
     */
    public function __construct()
    {
        $this->productoModel = new ProductoModel();
    }

    /**
     * Verifica que exista sesión activa de administrador; redirige al login si no.
     *
     * @return void
     */
    private function verificarSesion(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin'])) {
            header('Location: /PROYECTO_FINAL_DWA/login');
            exit;
        }
    }

    /**
     * Lista productos con paginación.
     *
     * @return void
     */
    public function index(): void
    {
        $this->verificarSesion();

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $limite = 10;
        $offset = ($pagina - 1) * $limite;

        $productos = $this->productoModel->obtenerPaginados($limite, $offset);
        $total = $this->productoModel->contarTodos();
        $totalPaginas = (int) ceil($total / $limite);

        require_once __DIR__ . '/../views/productos/index.php';
    }

    /**
     * Muestra el formulario de creación de producto.
     *
     * @return void
     */
    public function create(): void
    {
        $this->verificarSesion();
        Security::generarTokenCSRF();

        require_once __DIR__ . '/../views/productos/create.php';
    }

    /**
     * Procesa el formulario de creación de producto.
     *
     * Valida campos obligatorios, numéricos, negativos, precio de venta,
     * SKU duplicado e imagen. Registra en log si se crea exitosamente.
     *
     * @return void
     */
    public function store(): void
    {
        $this->verificarSesion();

        if (!Security::validarCSRF()) {
            Security::csrfDenegado();
            return;
        }

        $data = [
            'sku' => trim($_POST['sku'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'precio_compra' => trim($_POST['precio_compra'] ?? ''),
            'precio_venta' => trim($_POST['precio_venta'] ?? ''),
            'existencia' => trim($_POST['existencia'] ?? ''),
            'imagen' => ''
        ];

        if (
            $data['sku'] === '' ||
            $data['nombre'] === '' ||
            $data['descripcion'] === '' ||
            $data['precio_compra'] === '' ||
            $data['precio_venta'] === '' ||
            $data['existencia'] === ''
        ) {
            $_SESSION['error'] = 'Todos los campos son obligatorios.';
            header('Location: /PROYECTO_FINAL_DWA/productos/create');
            exit;
        }

        if (
            !is_numeric($data['precio_compra']) ||
            !is_numeric($data['precio_venta']) ||
            !is_numeric($data['existencia'])
        ) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia deben ser numericos.';
            header('Location: /PROYECTO_FINAL_DWA/productos/create');
            exit;
        }

        if (
            (float)$data['precio_compra'] < 0 ||
            (float)$data['precio_venta'] < 0 ||
            (int)$data['existencia'] < 0
        ) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: /PROYECTO_FINAL_DWA/productos/create');
            exit;
        }

        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor que el precio de compra.';
            header('Location: /PROYECTO_FINAL_DWA/productos/create');
            exit;
        }

        if ($this->productoModel->existeSku($data['sku'])) {
            $_SESSION['error'] = 'El SKU ya existe en el sistema.';
            header('Location: /PROYECTO_FINAL_DWA/productos/create');
            exit;
        }

        $imagen = $this->procesarImagen();

        if ($imagen === false) {
            return;
        }

        $data['imagen'] = $imagen;

        if ($this->productoModel->crear($data)) {
            Logger::registrar('CREAR', "SKU: {$data['sku']}, Nombre: {$data['nombre']}");
            $_SESSION['success'] = 'Producto registrado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible registrar el producto.';
        }

        header('Location: /PROYECTO_FINAL_DWA/productos');
        exit;
    }

    /**
     * Muestra el formulario de edición para un producto existente.
     *
     * @return void
     */
    public function edit(): void
    {
        $this->verificarSesion();
        Security::generarTokenCSRF();

        $id = (int)($_GET['id'] ?? 0);
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            header('Location: /PROYECTO_FINAL_DWA/productos');
            exit;
        }

        require_once __DIR__ . '/../views/productos/edit.php';
    }

    /**
     * Procesa el formulario de actualización de producto.
     *
     * Valida los mismos criterios que `store()` y además
     * maneja la imagen existente al reemplazarla.
     *
     * @return void
     */
    public function update(): void
    {
        $this->verificarSesion();

        if (!Security::validarCSRF()) {
            Security::csrfDenegado();
            return;
        }

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
            $_SESSION['error'] = 'ID invalido.';
            header('Location: /PROYECTO_FINAL_DWA/productos');
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
            header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . $id);
            exit;
        }

        if (
            !is_numeric($data['precio_compra']) ||
            !is_numeric($data['precio_venta']) ||
            !is_numeric($data['existencia'])
        ) {
            $_SESSION['error'] = 'Precio de compra, precio de venta y existencia deben ser numericos.';
            header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . $id);
            exit;
        }

        if (
            (float)$data['precio_compra'] < 0 ||
            (float)$data['precio_venta'] < 0 ||
            (int)$data['existencia'] < 0
        ) {
            $_SESSION['error'] = 'No se permiten valores negativos.';
            header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . $id);
            exit;
        }

        if ((float)$data['precio_venta'] < (float)$data['precio_compra']) {
            $_SESSION['error'] = 'El precio de venta no puede ser menor que el precio de compra.';
            header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . $id);
            exit;
        }

        if ($this->productoModel->existeSku($data['sku'], $id)) {
            $_SESSION['error'] = 'El SKU ya existe en otro producto.';
            header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . $id);
            exit;
        }

        $productoActual = $this->productoModel->obtenerPorId($id);
        $imagen = $this->procesarImagen($productoActual['imagen'] ?? '');

        if ($imagen === false) {
            return;
        }

        $data['imagen'] = $imagen;

        if ($this->productoModel->actualizar($id, $data)) {
            Logger::registrar('ACTUALIZAR', "ID: {$id}, SKU: {$data['sku']}, Nombre: {$data['nombre']}");
            $_SESSION['success'] = 'Producto actualizado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible actualizar el producto.';
        }

        header('Location: /PROYECTO_FINAL_DWA/productos');
        exit;
    }

    /**
     * Elimina un producto por ID.
     *
     * @return void
     */
    public function delete(): void
    {
        $this->verificarSesion();

        if (!Security::validarCSRF()) {
            Security::csrfDenegado();
            return;
        }

        $id = (int)($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error'] = 'ID invalido.';
            header('Location: /PROYECTO_FINAL_DWA/productos');
            exit;
        }

        $producto = $this->productoModel->obtenerPorId($id);

        if ($this->productoModel->eliminar($id)) {
            Logger::registrar(
                'ELIMINAR',
                "ID: {$id}, SKU: " . ($producto['sku'] ?? 'N/A') . ", Nombre: " . ($producto['nombre'] ?? 'N/A')
            );

            $_SESSION['success'] = 'Producto eliminado correctamente.';
        } else {
            $_SESSION['error'] = 'No fue posible eliminar el producto.';
        }

        header('Location: /PROYECTO_FINAL_DWA/productos');
        exit;
    }

    /**
     * Muestra el visor de logs administrativos con paginación.
     *
     * Parsea cada línea del log en el controller para mantener
     * la vista libre de lógica de negocio.
     *
     * @return void
     */
    public function logs(): void
    {
        $this->verificarSesion();

        $archivo = __DIR__ . '/../logs/admin.log';
        $lineas = [];

        if (file_exists($archivo)) {
            $contenido = file_get_contents($archivo);
            $lineasCrudas = array_filter(explode("\n", $contenido));
            $lineasCrudas = array_reverse($lineasCrudas);

            foreach ($lineasCrudas as $linea) {
                $parsed = [
                    'fecha' => '',
                    'usuario' => '',
                    'accion' => '',
                    'detalles' => '',
                ];

                if (preg_match('/^\[(.*?)\]\s\[(.*?)\]\s(.*?)(?:\s\|\s(.*))?$/', $linea, $m)) {
                    $parsed['fecha'] = $m[1];
                    $parsed['usuario'] = $m[2];
                    $parsed['accion'] = $m[3];
                    $parsed['detalles'] = $m[4] ?? '';
                } else {
                    $parsed['accion'] = $linea;
                }

                $parsed['badgeClass'] = match ($parsed['accion']) {
                    'LOGIN' => 'bg-success',
                    'LOGIN_FALLIDO' => 'bg-warning text-dark',
                    'LOGOUT' => 'bg-secondary',
                    'CREAR' => 'bg-primary',
                    'ACTUALIZAR' => 'bg-info text-dark',
                    'ELIMINAR' => 'bg-danger',
                    default => 'bg-dark',
                };

                $lineas[] = $parsed;
            }
        }

        $pagina = max(1, (int)($_GET['pagina'] ?? 1));
        $limite = 50;
        $total = count($lineas);
        $totalPaginas = max(1, (int) ceil($total / $limite));
        $offset = ($pagina - 1) * $limite;
        $lineas = array_slice($lineas, $offset, $limite);

        require_once __DIR__ . '/../views/productos/logs.php';
    }

    /**
     * Procesa la subida de imagen desde $_FILES.
     *
     * Valida tipo MIME, tamaño máximo (2 MB) y errores de subida.
     * Si se proporciona una imagen anterior, la elimina al reemplazarla.
     *
     * @param string $imagenActual Nombre del archivo de imagen existente (para reemplazo)
     * @return string|false Nombre del nuevo archivo, `false` si hay error
     */
    private function procesarImagen(string $imagenActual = ''): string|false
    {
        if (empty($_FILES['imagen']['name'])) {
            return $imagenActual;
        }

        $archivo = $_FILES['imagen'];

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = 'Error al subir la imagen. Codigo: ' . $archivo['error'];

            if ($imagenActual === '') {
                header('Location: /PROYECTO_FINAL_DWA/productos/create');
            } else {
                header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . ($_POST['id'] ?? 0));
            }

            exit;
        }

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $tiposPermitidos)) {
            $_SESSION['error'] = 'Solo se permiten imagenes JPEG, PNG, GIF y WebP.';

            if ($imagenActual === '') {
                header('Location: /PROYECTO_FINAL_DWA/productos/create');
            } else {
                header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . ($_POST['id'] ?? 0));
            }

            exit;
        }

        $maxSize = 2 * 1024 * 1024;

        if ($archivo['size'] > $maxSize) {
            $_SESSION['error'] = 'La imagen no debe superar los 2 MB.';

            if ($imagenActual === '') {
                header('Location: /PROYECTO_FINAL_DWA/productos/create');
            } else {
                header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . ($_POST['id'] ?? 0));
            }

            exit;
        }

        $extension = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        };

        $nombreArchivo = 'prod_' . time() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        $ruta = __DIR__ . '/../views/img/productos/' . $nombreArchivo;

        if (!move_uploaded_file($archivo['tmp_name'], $ruta)) {
            $_SESSION['error'] = 'No se pudo guardar la imagen.';

            if ($imagenActual === '') {
                header('Location: /PROYECTO_FINAL_DWA/productos/create');
            } else {
                header('Location: /PROYECTO_FINAL_DWA/productos/edit/' . ($_POST['id'] ?? 0));
            }

            exit;
        }

        if ($imagenActual !== '') {
            $rutaAnterior = __DIR__ . '/../views/img/productos/' . $imagenActual;

            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

        return $nombreArchivo;
    }
}