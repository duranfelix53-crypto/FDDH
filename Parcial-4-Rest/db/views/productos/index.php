<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Administración de productos</h2>
    <div>
        <a href="index.php?route=productos/create" class="btn btn-success">
            Nuevo producto
        </a>
        <a href="index.php?route=logout" class="btn btn-danger">
            Cerrar sesión
        </a>
        <a href="index.php?route=bitacora" class="btn btn-info btn-sm">
    Bitácora
</a>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>SKU</th>
            <th>Nombre</th>
            <th>Imagen</th>
            <th>Precio compra</th>
            <th>Precio venta</th>
            <th>Existencia</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $producto): ?>
            <tr>
                <td><?= $producto['id']; ?></td>
                <td><?= htmlspecialchars($producto['sku']); ?></td>
                <td><?= htmlspecialchars($producto['nombre']); ?></td>
                <td>
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="uploads/productos/<?= htmlspecialchars($producto['imagen']); ?>"
                             width="60" class="rounded">
                    <?php else: ?>
                        <span class="text-muted">—</span>
                    <?php endif; ?>
                </td>
                <td><?= number_format((float)$producto['precio_compra'], 2); ?></td>
                <td><?= number_format((float)$producto['precio_venta'], 2); ?></td>
                <td><?= (int)$producto['existencia']; ?></td>
                <td>
                    <a href="index.php?route=productos/edit&id=<?= $producto['id']; ?>" 
                       class="btn btn-primary btn-sm">
                        Editar
                    </a>
                    <form action="index.php?route=productos/delete" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?= $producto['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('¿Deseas eliminar este producto?');">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Paginación -->
<nav class="mt-3">
    <ul class="pagination justify-content-center">

        <li class="page-item <?= $pagina <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?route=productos&pagina=<?= $pagina - 1; ?>">
                Anterior
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : ''; ?>">
                <a class="page-link" href="index.php?route=productos&pagina=<?= $i; ?>">
                    <?= $i; ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?route=productos&pagina=<?= $pagina + 1; ?>">
                Siguiente
            </a>
        </li>

    </ul>
</nav>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>