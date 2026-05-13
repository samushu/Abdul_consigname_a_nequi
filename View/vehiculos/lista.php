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

$badgeEstado = [
    'DISPONIBLE'    => 'badge-verde',
    'ALQUILADO'     => 'badge-rojo',
    'MANTENIMIENTO' => 'badge-amber',
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
                <th>Vehículo</th>
                <th>Año</th>
                <th>Placa</th>
                <th>Estado</th>
                <th>Precio / Día</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($datos['vehiculos'])): ?>
            <tr><td colspan="7" class="tabla-vacia">No hay vehículos registrados aún.</td></tr>
        <?php else: ?>
            <?php foreach ($datos['vehiculos'] as $v): ?>
            <tr>
                <td class="td-id"><?= $v['id'] ?></td>
                <td>
                    <div class="vehiculo-cell">
                        <div class="vehiculo-thumb">
                            <?php if (!empty($v['imagen'])): ?>
                                <img src="View/assets/img/uploads/<?= htmlspecialchars($v['imagen']) ?>"
                                     alt="<?= htmlspecialchars($v['marca'].' '.$v['modelo']) ?>"
                                     class="vehiculo-img">
                            <?php else: ?>
                                <div class="vehiculo-sin-img">
                                    <span class="vehiculo-sin-img-ico">📷</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="vehiculo-info">
                            <strong><?= htmlspecialchars($v['marca']) ?> <?= htmlspecialchars($v['modelo']) ?></strong>
                            <small class="cat-label"><?= $v['categoria'] ?></small>
                        </div>
                    </div>
                </td>
                <td><?= $v['anio'] ?></td>
                <td><code class="placa-code"><?= htmlspecialchars($v['placa']) ?></code></td>
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