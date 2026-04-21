<?php
// View/vehiculos/lista.php
$pageTitle = 'Vehículos';
require_once __DIR__ . '/../shared/header.php';

$mensajes = [
    'creado'           => ['tipo' => 'exito', 'txt' => '✓ Vehículo registrado correctamente.'],
    'editado'          => ['tipo' => 'exito', 'txt' => '✓ Vehículo actualizado.'],
    'eliminado'        => ['tipo' => 'exito', 'txt' => '✓ Vehículo eliminado.'],
    'reservas_activas' => ['tipo' => 'error', 'txt' => '✗ No se puede eliminar: el vehículo tiene reservas activas.'],
    'db'               => ['tipo' => 'error', 'txt' => '✗ Error en la base de datos.'],
];

$iconoCategoria = ['AUTOMOVIL' => '🚗', 'CAMIONETA' => '🛻', 'MOTO' => '🏍️'];
$badgeEstado    = [
    'DISPONIBLE'   => 'badge-verde',
    'ALQUILADO'    => 'badge-rojo',
    'MANTENIMIENTO'=> 'badge-amber',
];
?>

<div class="page-header">
    <h1 class="page-title">Vehículos <span>·</span> Listado</h1>
    <a href="index.php?modulo=vehiculos&accion=nuevo" class="btn btn-rojo">+ Nuevo Vehículo</a>
</div>

<?php if ($datos['exito'] && isset($mensajes[$datos['exito']])): ?>
    <div class="alerta alerta-exito"><?= $mensajes[$datos['exito']]['txt'] ?></div>
<?php endif; ?>
<?php if ($datos['error'] && isset($mensajes[$datos['error']])): ?>
    <div class="alerta alerta-error"><?= $mensajes[$datos['error']]['txt'] ?></div>
<?php endif; ?>

<div class="tabla-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Categoría</th>
                <th>Marca / Modelo</th>
                <th>Año</th>
                <th>Placa</th>
                <th>Estado</th>
                <th>Precio / Día</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($datos['vehiculos'])): ?>
            <tr><td colspan="8" class="tabla-vacia">No hay vehículos registrados aún.</td></tr>
        <?php else: ?>
            <?php foreach ($datos['vehiculos'] as $v): ?>
            <tr>
                <td style="color:var(--gris-lite)"><?= $v['id'] ?></td>
                <td>
                    <span class="ico"><?= $iconoCategoria[$v['categoria']] ?? '🚘' ?></span>
                    <small style="color:var(--gris-lite)"><?= $v['categoria'] ?></small>
                </td>
                <td><strong><?= htmlspecialchars($v['marca']) ?> <?= htmlspecialchars($v['modelo']) ?></strong></td>
                <td><?= $v['anio'] ?></td>
                <td><code style="background:var(--gris-med);padding:.1rem .45rem;border-radius:4px"><?= htmlspecialchars($v['placa']) ?></code></td>
                <td><span class="badge <?= $badgeEstado[$v['estado']] ?? 'badge-gris' ?>"><?= $v['estado'] ?></span></td>
                <td>$ <?= number_format($v['precio_dia'], 0, ',', '.') ?></td>
                <td>
                    <div class="acciones">
                        <a href="index.php?modulo=vehiculos&accion=editar&id=<?= $v['id'] ?>"
                           class="btn btn-gris btn-sm">Editar</a>
                        <a href="index.php?modulo=vehiculos&accion=eliminar&id=<?= $v['id'] ?>"
                           class="btn btn-rojo btn-sm"
                           onclick="return confirm('¿Eliminar este vehículo?')">Eliminar</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
