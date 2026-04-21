<?php
// View/reservas/formulario.php
$pageTitle = 'Nueva Reserva';
require_once __DIR__ . '/../shared/header.php';

$prev    = $_SESSION['form_data']    ?? [];
$errores = $_SESSION['form_errores'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errores']);

function err(array $errs, string $key): string {
    return isset($errs[$key]) ? '<span class="error-msg">'.$errs[$key].'</span>' : '';
}
?>

<div class="page-header">
    <h1 class="page-title">Nueva <span>Reserva</span></h1>
    <a href="index.php?modulo=reservas" class="btn btn-gris">← Volver</a>
</div>

<div class="form-card">
    <form method="POST" action="index.php?modulo=reservas&accion=crear">

        <div class="form-grid">
            <div class="campo full">
                <label for="cliente_id">Cliente</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">-- Selecciona un cliente --</option>
                    <?php foreach ($datos['clientes'] as $c): ?>
                        <option value="<?= $c['id'] ?>"
                            <?= (($prev['cliente_id'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nombre']) ?> · <?= htmlspecialchars($c['documento']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err($errores, 'cliente_id') ?>
            </div>

            <div class="campo full">
                <label for="vehiculo_id">Vehículo disponible</label>
                <select id="vehiculo_id" name="vehiculo_id" required>
                    <option value="">-- Selecciona un vehículo --</option>
                    <?php
                    $iconoCat = ['AUTOMOVIL' => '🚗', 'CAMIONETA' => '🛻', 'MOTO' => '🏍️'];
                    foreach ($datos['vehiculos'] as $v): ?>
                        <option value="<?= $v['id'] ?>"
                                data-precio="<?= $v['precio_dia'] ?>"
                            <?= (($prev['vehiculo_id'] ?? '') == $v['id']) ? 'selected' : '' ?>>
                            <?= ($iconoCat[$v['categoria']] ?? '🚘') ?>
                            <?= htmlspecialchars($v['marca']) ?> <?= htmlspecialchars($v['modelo']) ?>
                            (<?= $v['placa'] ?>) · $<?= number_format($v['precio_dia'],0,',','.') ?>/día
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err($errores, 'vehiculo_id') ?>
            </div>

            <div class="campo">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio"
                       value="<?= htmlspecialchars($prev['fecha_inicio'] ?? $datos['hoy']) ?>"
                       min="<?= $datos['hoy'] ?>" required>
                <?= err($errores, 'fecha_inicio') ?>
            </div>

            <div class="campo">
                <label for="fecha_fin">Fecha de fin</label>
                <input type="date" id="fecha_fin" name="fecha_fin"
                       value="<?= htmlspecialchars($prev['fecha_fin'] ?? $datos['hoy']) ?>"
                       min="<?= $datos['hoy'] ?>" required>
                <?= err($errores, 'fecha_fin') ?>
            </div>

            <!-- Resumen de costo -->
            <div class="campo full" id="resumen-wrap" style="display:none">
                <div style="background:var(--gris-med);border-radius:var(--radio);padding:1rem;
                            display:flex;gap:2rem;align-items:center;flex-wrap:wrap">
                    <div>
                        <div style="font-family:var(--font-h);font-size:.75rem;color:var(--gris-lite);
                                    text-transform:uppercase;letter-spacing:.5px">Días</div>
                        <div id="resumen-dias" style="font-size:1.4rem;font-weight:700"></div>
                    </div>
                    <div>
                        <div style="font-family:var(--font-h);font-size:.75rem;color:var(--gris-lite);
                                    text-transform:uppercase;letter-spacing:.5px">Precio / Día</div>
                        <div id="resumen-precio" style="font-size:1.4rem;font-weight:700"></div>
                    </div>
                    <div>
                        <div style="font-family:var(--font-h);font-size:.75rem;color:var(--gris-lite);
                                    text-transform:uppercase;letter-spacing:.5px">Total estimado</div>
                        <div id="resumen-total" style="font-size:1.6rem;font-weight:800;color:var(--rojo-claro)"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn btn-rojo">+ Confirmar Reserva</button>
            <a href="index.php?modulo=reservas" class="btn btn-gris">Cancelar</a>
        </div>
    </form>
</div>

<script>
// Cálculo en tiempo real del costo estimado
const selVeh    = document.getElementById('vehiculo_id');
const inpInicio = document.getElementById('fecha_inicio');
const inpFin    = document.getElementById('fecha_fin');
const wrap      = document.getElementById('resumen-wrap');

function calcular() {
    const opt    = selVeh.options[selVeh.selectedIndex];
    const precio = parseFloat(opt?.dataset?.precio || 0);
    const ini    = new Date(inpInicio.value);
    const fin    = new Date(inpFin.value);

    if (!precio || isNaN(ini) || isNaN(fin) || fin < ini) {
        wrap.style.display = 'none'; return;
    }

    const dias  = Math.floor((fin - ini) / 86400000) + 1;
    const total = dias * precio;
    const fmt   = n => '$' + n.toLocaleString('es-CO');

    document.getElementById('resumen-dias').textContent   = dias;
    document.getElementById('resumen-precio').textContent = fmt(precio);
    document.getElementById('resumen-total').textContent  = fmt(total);
    wrap.style.display = 'block';
}

selVeh.addEventListener('change', calcular);
inpInicio.addEventListener('change', calcular);
inpFin.addEventListener('change', calcular);

// Sincronizar mínimo de fecha_fin con fecha_inicio
inpInicio.addEventListener('change', () => {
    inpFin.min = inpInicio.value;
    if (inpFin.value < inpInicio.value) inpFin.value = inpInicio.value;
    calcular();
});

calcular(); // correr al cargar si hay valores previos
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
