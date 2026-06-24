<?php
/**
 * Panel de administración de productos.
 *
 * Lista todos los productos en una tabla con acciones de editar y eliminar.
 * Incluye controles de paginación cuando hay más de una página.
 *
 * @var array $productos Lista de productos de la página actual
 * @var int $pagina Número de página actual
 * @var int $totalPaginas Total de páginas
 */
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Administracion de productos</h2>
    <div>
        <a href="productos/create" class="btn btn-success">Nuevo producto</a>
        <a href="productos/logs" class="btn btn-info">Bitacora</a>
        <a href="logout" class="btn btn-danger">Cerrar sesion</a>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Imagen</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Precio compra</th>
            <th>Precio venta</th>
            <th>Existencia</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?= (int)$producto['id']; ?></td>
                <td>
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="views/img/productos/<?= htmlspecialchars($producto['imagen']); ?>" alt="img" style="max-height: 50px;">
                    <?php else: ?>
                        <span class="text-muted">---</span>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($producto['sku']); ?></td>
                <td><?= htmlspecialchars($producto['nombre']); ?></td>
                <td><?= number_format((float)$producto['precio_compra'], 2); ?></td>
                <td><?= number_format((float)$producto['precio_venta'], 2); ?></td>
                <td><?= (int)$producto['existencia']; ?></td>
                <td>
                    <a href="productos/edit/<?= (int)$producto['id']; ?>"
                       class="btn btn-primary btn-sm">Editar</a>

                    <form action="productos/delete" method="POST" class="d-inline">
                        <?= \Helpers\Security::campoCSRF(); ?>
                        <input type="hidden" name="id" value="<?= (int)$producto['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Deseas eliminar este producto?');">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPaginas > 1): ?>
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="productos/pagina/<?= $i; ?>"><?= $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>