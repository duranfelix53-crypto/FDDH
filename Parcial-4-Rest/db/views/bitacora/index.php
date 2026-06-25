<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<h2>Bitácora de acciones</h2>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $registro): ?>
            <tr>
                <td><?= $registro['id']; ?></td>
                <td><?= htmlspecialchars($registro['usuario_nombre']); ?></td>
                <td><?= htmlspecialchars($registro['accion']); ?></td>
                <td><?= $registro['fecha']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
