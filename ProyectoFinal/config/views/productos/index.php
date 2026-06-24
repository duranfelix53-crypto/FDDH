/* Este fragmento de código PHP forma parte de una aplicación web que administra productos. */
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Administración de productos</h2>
    <div>
        <a href="index.php?route=productos/create" class="btn btn-success">Nuevo producto</a>
        <a href="index.php?route=productos/bitacora" class="btn btn-info">Bitácora</a>
        <a href="index.php?route=logout" class="btn btn-danger">Cerrar sesión</a>
    </div>
</div>

<?php if (isset($_SESSION["error"])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION["error"]); ?>
        <?php unset($_SESSION["error"]); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION["success"])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION["success"]); ?>
        <?php unset($_SESSION["success"]); ?>
    </div>
<?php endif; ?>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
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
                <td><?= htmlspecialchars($producto['sku']); ?></td>
                <td><?= htmlspecialchars($producto['nombre']); ?></td>
                <td><?= number_format((float)$producto['precio_compra'], 2); ?></td>
                <td><?= number_format((float)$producto['precio_venta'], 2); ?></td>
                <td><?= (int)$producto['existencia']; ?></td>
                <td>
                    <a href="index.php?route=productos/edit&id=<?= (int)$producto['id']; ?>"
                    class="btn btn-primary btn-sm px-3">Editar</a>

                    <form action="index.php?route=productos/delete" method="POST" class="d-inline">
                        <input type="hidden" name="id" value="<?= (int)$producto['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-sm px-3"
                            onclick="return confirm('¿Deseas eliminar este producto?');">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php if ($totalPaginas > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <li class="page-item <?= $paginaActual <= 1 ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?route=productos&pagina=<?= $paginaActual - 1; ?>">Anterior</a>
        </li>

        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
        <li class="page-item <?= $i === $paginaActual ? 'active' : ''; ?>">
            <a class="page-link" href="index.php?route=productos&pagina=<?= $i; ?>"><?= $i; ?></a>
        </li>
        <?php endfor; ?>

        <li class="page-item <?= $paginaActual >= $totalPaginas ? 'disabled' : ''; ?>">
            <a class="page-link" href="index.php?route=productos&pagina=<?= $paginaActual + 1; ?>">Siguiente</a>
        </li>
    </ul>
</nav>
<p class="text-center text-muted">
    Página <?= $paginaActual; ?> de <?= $totalPaginas; ?> — <?= $total; ?> productos en total
</p>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>