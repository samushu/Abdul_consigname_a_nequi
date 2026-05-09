<?php
// View/clientes/lista.php
$pageTitle = 'Clientes';
require_once __DIR__ . '/../shared/header.php';

$mensajes = [
    'creado'           => ['tipo' => 'exito', 'txt' => '✓ Cliente registrado correctamente.'],
    'editado'          => ['tipo' => 'exito', 'txt' => '✓ Cliente actualizado correctamente.'],
    'eliminado'        => ['tipo' => 'exito', 'txt' => '✓ Cliente eliminado.'],
    'reservas_activas' => ['tipo' => 'error', 'txt' => '✗ No se puede eliminar: el cliente tiene reservas activas.'],
    'db'               => ['tipo' => 'error', 'txt' => '✗ Error en la base de datos. Intenta de nuevo.'],
];
?>

<div class="page-header">
    <h1 class="page-title">Clientes <span>·</span> Listado</h1>
    <a href="index.php?modulo=clientes&accion=nuevo" class="btn btn-rojo">+ Nuevo Cliente</a>
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
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Licencia</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
        <?php if (empty($datos['clientes'])): ?>
            <tr><td colspan="7" class="tabla-vacia">No hay clientes registrados aún.</td></tr>
        <?php else: ?>
            <?php foreach ($datos['clientes'] as $c): ?>
            <tr>
                <td class="td-id"><?= $c['id'] ?></td>
                <td><strong><?= htmlspecialchars($c['nombre']) ?></strong></td>
                <td><?= htmlspecialchars($c['documento']) ?></td>
                <td><?= htmlspecialchars($c['telefono']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><span class="badge badge-gris"><?= htmlspecialchars($c['licencia']) ?></span></td>
                <td>
                    <div class="acciones">
                        <a href="index.php?modulo=clientes&accion=editar&id=<?= $c['id'] ?>"
                           class="btn btn-gris btn-sm">Editar</a>
                        <a href="index.php?modulo=clientes&accion=eliminar&id=<?= $c['id'] ?>"
                           class="btn btn-rojo btn-sm"
                           onclick="return confirm('¿Eliminar a <?= addslashes(htmlspecialchars($c['nombre'])) ?>?')">Eliminar</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
