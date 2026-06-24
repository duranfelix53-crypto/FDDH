<?php
/**
 * Formulario de creación de producto.
 *
 * Renderiza campos para SKU, nombre, descripción, precio de compra,
 * precio de venta, existencia y carga de imagen. Envía por POST a productos/store.
 */
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Registrar producto</h2>

<form action="productos/store" method="POST" enctype="multipart/form-data">
    <?= \Helpers\Security::campoCSRF(); ?>

    <div class="mb-3">
        <label class="form-label">SKU</label>
        <input type="text" name="sku" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Descripcion</label>
        <textarea name="descripcion" class="form-control" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label">Precio compra</label>
       <input type="number" step="0.01" name="precio_compra" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Precio venta</label>
       <input type="number" step="0.01" name="precio_venta" class="form-control" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Existencia</label>
        <input type="number" name="existencia" class="form-control" required min="0">
    </div>

    <div class="mb-3">
        <label class="form-label">Imagen del producto</label>
        <input type="file" name="imagen" class="form-control" accept="image/jpeg,image/png,image/gif,image/webp">
    </div>

    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="productos" class="btn btn-secondary">Cancelar</a>
</form>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>