<?php
/**
 * Vista del catálogo público de productos.
 *
 * Muestra una cuadrícula de tarjetas de producto con búsqueda por nombre/descripción,
 * paginación, imagen, SKU, precio y existencia.
 *
 * @var array $productos Productos de la página actual
 * @var string $termino Término de búsqueda actual
 * @var int $pagina Número de página actual
 * @var int $totalPaginas Total de páginas
 */
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Catalogo publico de productos</h2>
        <p>Consulta los productos disponibles y realiza busquedas por nombre o descripcion.</p>
    </div>
</div>

<form onsubmit="event.preventDefault(); var t=this.buscar.value.trim(); window.location=t?'catalogo/buscar/'+encodeURIComponent(t):'catalogo';" class="row g-2 mb-4">

    <div class="col-md-10">
        <input type="text" name="buscar" class="form-control"
               placeholder="Buscar por nombre o descripcion"
               value="<?= htmlspecialchars($termino ?? ''); ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
    </div>
</form>

<div class="row">
    <?php if (!empty($productos)): ?>
        <?php foreach ($productos as $producto): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <?php if (!empty($producto['imagen'])): ?>
                        <img src="views/img/productos/<?= htmlspecialchars($producto['imagen']); ?>" class="card-img-top" alt="<?= htmlspecialchars($producto['nombre']); ?>" style="height: 200px; object-fit: cover;">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($producto['nombre']); ?></h5>
                        <h6 class="card-subtitle mb-2 text-muted">SKU: <?= htmlspecialchars($producto['sku']); ?></h6>
                        <p class="card-text"><?= htmlspecialchars($producto['descripcion']); ?></p>
                        <p><strong>Precio:</strong> $<?= number_format((float)$producto['precio_venta'], 2); ?></p>
                        <p><strong>Existencia:</strong> <?= (int)$producto['existencia']; ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-warning">No se encontraron productos.</div>
        </div>
    <?php endif; ?>
</div>

<?php if ($totalPaginas > 1): ?>
<nav class="mt-3">
    <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
            <li class="page-item <?= $i === $pagina ? 'active' : ''; ?>">
                <?php if (!empty($termino)): ?>
                    <a class="page-link" href="catalogo/buscar/<?= urlencode($termino); ?>/pagina/<?= $i; ?>"><?= $i; ?></a>
                <?php else: ?>
                    <a class="page-link" href="catalogo/pagina/<?= $i; ?>"><?= $i; ?></a>
                <?php endif; ?>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>