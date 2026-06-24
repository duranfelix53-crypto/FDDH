<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php /** @var array $registros */ ?>

/* Este fragmento de código PHP muestra una tabla de registros de actividad 
(bitácora de acciones) con detalles como ID, Usuario, Acción, Detalle, IP y Fecha. /*

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Bitácora de acciones</h2>
    <a href="index.php?route=productos" class="btn btn-secondary">Volver</a>
</div>

<table class="table table-bordered table-striped table-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Acción</th>
            <th>Detalle</th>
            <th>IP</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $registro): ?>
        <tr>
            <td><?= (int)$registro["id"]; ?></td>
            <td><?= htmlspecialchars($registro["usuario"]); ?></td>
            <td>
                <?php
                $badges = [
                    "LOGIN"   => "success",
                    "LOGOUT"  => "secondary",
                    "CREAR"   => "primary",
                    "EDITAR"  => "warning",
                    "ELIMINAR"=> "danger"
                ];
                $color = $badges[$registro["accion"]] ?? "dark";
                ?>
                <span class="badge bg-<?= $color; ?>">
                    <?= htmlspecialchars($registro["accion"]); ?>
                </span>
            </td>
            <td><?= htmlspecialchars($registro["detalle"]); ?></td>
            <td><?= htmlspecialchars($registro["ip"]); ?></td>
            <td><?= htmlspecialchars($registro["fecha"]); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>