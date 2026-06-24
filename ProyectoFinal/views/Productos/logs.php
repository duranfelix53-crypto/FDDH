<?php
/**
 * Visor de bitácora de acciones administrativas.
 *
 * Recibe líneas ya parseadas desde el controlador.
 *
 * @var array $lineas  Arreglo de líneas parseadas con claves: fecha, usuario, accion, detalles, badgeClass
 * @var int   $offset  Desplazamiento para numeración de líneas
 * @var int   $pagina  Página actual
 * @var int   $totalPaginas Total de páginas
 */
?>

<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Bitacora de acciones</h2>
    <a href="productos" class="btn btn-secondary">Volver a productos</a>
</div>

<div class="card">
    <div class="card-body">
        <?php if (empty($lineas)): ?>
            <div class="alert alert-info">No hay registros en la bitacora.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-sm table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Accion</th>
                            <th>Detalles</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lineas as $i => $linea): ?>
                            <tr>
                                <td><?= $offset + $i + 1; ?></td>
                                <td><?= htmlspecialchars($linea['fecha']); ?></td>
                                <td><?= htmlspecialchars($linea['usuario']); ?></td>
                                <td><span class="badge <?= $linea['badgeClass']; ?>"><?= htmlspecialchars($linea['accion']); ?></span></td>
                                <td><?= htmlspecialchars($linea['detalles']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if ($totalPaginas > 1): ?>
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                        <li class="page-item <?= $i === $pagina ? 'active' : ''; ?>">
                            <a class="page-link" href="productos/logs/pagina/<?= $i; ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>