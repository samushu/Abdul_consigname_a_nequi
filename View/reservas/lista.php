<?php
// View/reservas/lista.php
$pageTitle = 'Reservas';
require_once __DIR__ . '/../shared/header.php';

$mensajes = [
    'creada'       => ['tipo' => 'exito', 'txt' => '✓ Reserva creada correctamente.'],
    'finalizada'   => ['tipo' => 'exito', 'txt' => '✓ Reserva finalizada. Vehículo disponible.'],
    'cancelada'    => ['tipo' => 'exito', 'txt' => '✓ Reserva cancelada.'],
    'db'           => ['tipo' => 'error', 'txt' => '✗ Error en la base de datos.'],
];

$badgeReserva = [
    'ACTIVA'     => 'badge-verde',
    'FINALIZADA' => 'badge-gris',
    'CANCELADA'  => 'badge-rojo',
];
$iconoCat = ['AUTOMOVIL' => '🚗', 'CAMIONETA' => '🛻', 'MOTO' => '🏍️'];
?>

<div class="page-header">
    <h1 class="page-title">Reservas <span>·</span> Historial</h1>
    <a href="index.php?modulo=reservas&accion=nuevo" class="btn btn-rojo">+ Nueva Reserva</a>
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
                <th>Cliente</th>
                <th>Vehículo</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($datos['reservas'])): ?>
            <tr><td colspan="8" class="tabla-vacia">No hay reservas registradas aún.</td></tr>
        <?php else: ?>
            <?php foreach ($datos['reservas'] as $r): ?>
            <tr>
                <td style="color:var(--gris-lite)"><?= $r['id'] ?></td>
                <td>
                    <strong><?= htmlspecialchars($r['cliente_nombre']) ?></strong><br>
                    <small style="color:var(--gris-lite)"><?= htmlspecialchars($r['cliente_documento']) ?></small>
                </td>
                <td>
                    <span><?= $iconoCat[$r['vehiculo_categoria']] ?? '🚘' ?></span>
                    <?= htmlspecialchars($r['vehiculo_marca']) ?> <?= htmlspecialchars($r['vehiculo_modelo']) ?><br>
                    <code style="font-size:.78rem;background:var(--gris-med);padding:.1rem .3rem;border-radius:3px">
                        <?= htmlspecialchars($r['vehiculo_placa']) ?>
                    </code>
                </td>
                <td><?= date('d/m/Y', strtotime($r['fecha_inicio'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['fecha_fin'])) ?></td>
                <td style="font-weight:600">$ <?= number_format($r['total'], 0, ',', '.') ?></td>
                <td><span class="badge <?= $badgeReserva[$r['estado']] ?>"><?= $r['estado'] ?></span></td>
                <td>
                    <div class="acciones">
                    <?php if ($r['estado'] === 'ACTIVA'): ?>
                        <a href="index.php?modulo=reservas&accion=finalizar&id=<?= $r['id'] ?>"
                           class="btn btn-verde btn-sm"
                           onclick="return confirm('¿Marcar reserva #<?= $r['id'] ?> como finalizada?')">Finalizar</a>
                        <a href="index.php?modulo=reservas&accion=cancelar&id=<?= $r['id'] ?>"
                           class="btn btn-rojo btn-sm"
                           onclick="return confirm('¿Cancelar la reserva #<?= $r['id'] ?>?')">Cancelar</a>
                    <?php else: ?>
                        <span style="color:var(--gris-lite);font-size:.8rem">Sin acciones</span>
                    <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
