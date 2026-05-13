<?php
// View/vehiculos/formulario.php
$esEditar  = !empty($datos['id']);
$pageTitle = $esEditar ? 'Editar Vehículo' : 'Nuevo Vehículo';
require_once __DIR__ . '/../shared/header.php';

$prev    = $_SESSION['form_data']    ?? [];
$errores = $_SESSION['form_errores'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errores']);

$v = $datos['vehiculo'] ?? $prev;

function val(array $arr, string $key): string {
    return htmlspecialchars($arr[$key] ?? '');
}
function err(array $errs, string $key): string {
    return isset($errs[$key]) ? '<span class="error-msg">'.$errs[$key].'</span>' : '';
}
?>

<div class="page-header">
    <h1 class="page-title"><?= $esEditar ? 'Editar' : 'Nuevo' ?> <span>Vehículo</span></h1>
    <a href="index.php?modulo=vehiculos" class="btn btn-gris">← Volver</a>
</div>

<div class="form-card">
    <form method="POST"
          enctype="multipart/form-data"
          action="index.php?modulo=vehiculos&accion=<?= $esEditar ? 'editar&id='.(int)$datos['id'] : 'crear' ?>">

        <div class="form-grid">
            <div class="campo">
                <label for="marca">Marca</label>
                <input type="text" id="marca" name="marca"
                       value="<?= val($v, 'marca') ?>" placeholder="Toyota, Mazda…" required>
                <?= err($errores, 'marca') ?>
            </div>

            <div class="campo">
                <label for="modelo">Modelo</label>
                <input type="text" id="modelo" name="modelo"
                       value="<?= val($v, 'modelo') ?>" placeholder="Hilux 2024" required>
                <?= err($errores, 'modelo') ?>
            </div>

            <div class="campo">
                <label for="anio">Año</label>
                <input type="number" id="anio" name="anio"
                       value="<?= val($v, 'anio') ?>"
                       min="1990" max="<?= (int)date('Y') + 1 ?>" required>
                <?= err($errores, 'anio') ?>
            </div>

            <div class="campo">
                <label for="placa">Placa</label>
                <input type="text" id="placa" name="placa"
                       value="<?= val($v, 'placa') ?>" placeholder="ABC-123" required>
                <?= err($errores, 'placa') ?>
            </div>

            <div class="campo">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria" required>
                    <option value="">-- Selecciona --</option>
                    <?php foreach ($datos['categorias'] as $cat): ?>
                        <option value="<?= $cat ?>"
                            <?= (($v['categoria'] ?? '') === $cat) ? 'selected' : '' ?>>
                            <?= $cat ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err($errores, 'categoria') ?>
            </div>

            <div class="campo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <?php foreach ($datos['estados'] as $est): ?>
                        <option value="<?= $est ?>"
                            <?= (($v['estado'] ?? 'DISPONIBLE') === $est) ? 'selected' : '' ?>>
                            <?= $est ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err($errores, 'estado') ?>
            </div>

            <div class="campo full">
                <label for="precio_dia">Precio por día (COP)</label>
                <input type="number" id="precio_dia" name="precio_dia"
                       value="<?= val($v, 'precio_dia') ?>"
                       min="1" step="1" placeholder="150000" required>
                <?= err($errores, 'precio_dia') ?>
            </div>

            <!-- ── Foto del vehículo ───────────────────────── -->
            <div class="campo full">
                <label for="imagen">
                    Foto del vehículo
                    <span class="lbl-hint">
                        <?= $esEditar
                            ? '— dejar vacío para conservar la actual'
                            : '— opcional · JPG, PNG o WEBP · máx. 2 MB' ?>
                    </span>
                </label>

                <!-- Preview: imagen actual (editar) o preview de nueva selección -->
                <div class="img-preview-wrap<?= ($esEditar && !empty($v['imagen'])) ? '' : ' oculto' ?>" id="preview-wrap">
                    <img id="img-preview"
                         src="<?= ($esEditar && !empty($v['imagen'])) ? 'View/assets/img/uploads/'.htmlspecialchars($v['imagen']) : '' ?>"
                         alt="Vista previa"
                         class="img-preview">
                </div>

                <label for="imagen" class="file-label">
                    <span id="file-nombre">
                        <?= ($esEditar && !empty($v['imagen'])) ? 'Cambiar imagen…' : 'Seleccionar imagen…' ?>
                    </span>
                    <input type="file" id="imagen" name="imagen"
                           accept="image/jpeg,image/png,image/webp"
                           class="file-input">
                </label>
                <?= err($errores, 'imagen') ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn btn-rojo">
                <?= $esEditar ? '💾 Guardar cambios' : '+ Registrar vehículo' ?>
            </button>
            <a href="index.php?modulo=vehiculos" class="btn btn-gris">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById('imagen').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    document.getElementById('file-nombre').textContent = file.name;

    const reader = new FileReader();
    reader.onload = function (e) {
        const preview = document.getElementById('img-preview');
        const wrap    = document.getElementById('preview-wrap');
        preview.src = e.target.result;
        wrap.classList.remove('oculto');
    };
    reader.readAsDataURL(file);
});
</script>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
