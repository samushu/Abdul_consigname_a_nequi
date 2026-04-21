<?php
// View/clientes/formulario.php
$esEditar  = !empty($datos['id']);
$pageTitle = $esEditar ? 'Editar Cliente' : 'Nuevo Cliente';
require_once __DIR__ . '/../shared/header.php';

// Soporte para re-mostrar datos tras error de validación
$prev    = $_SESSION['form_data']    ?? [];
$errores = $_SESSION['form_errores'] ?? [];
unset($_SESSION['form_data'], $_SESSION['form_errores']);

// Si estamos editando usamos los datos del modelo, sino los del POST previo
$v = $datos['cliente'] ?? $prev;

function val(array $arr, string $key): string {
    return htmlspecialchars($arr[$key] ?? '');
}
function err(array $errs, string $key): string {
    return isset($errs[$key]) ? '<span class="error-msg">'.$errs[$key].'</span>' : '';
}
?>

<div class="page-header">
    <h1 class="page-title"><?= $esEditar ? 'Editar' : 'Nuevo' ?> <span>Cliente</span></h1>
    <a href="index.php?modulo=clientes" class="btn btn-gris">← Volver</a>
</div>

<div class="form-card">
    <form method="POST"
          action="index.php?modulo=clientes&accion=<?= $esEditar ? 'editar&id='.(int)$datos['id'] : 'crear' ?>">

        <div class="form-grid">
            <div class="campo full">
                <label for="nombre">Nombre completo</label>
                <input type="text" id="nombre" name="nombre"
                       value="<?= val($v, 'nombre') ?>" placeholder="Ej: Juan Pérez" required>
                <?= err($errores, 'nombre') ?>
            </div>

            <div class="campo">
                <label for="documento">Documento</label>
                <input type="text" id="documento" name="documento"
                       value="<?= val($v, 'documento') ?>" placeholder="Cédula / NIT" required>
                <?= err($errores, 'documento') ?>
            </div>

            <div class="campo">
                <label for="licencia">N° Licencia</label>
                <input type="text" id="licencia" name="licencia"
                       value="<?= val($v, 'licencia') ?>" placeholder="LIC-001" required>
                <?= err($errores, 'licencia') ?>
            </div>

            <div class="campo">
                <label for="telefono">Teléfono</label>
                <input type="tel" id="telefono" name="telefono"
                       value="<?= val($v, 'telefono') ?>" placeholder="310 000 0000">
                <?= err($errores, 'telefono') ?>
            </div>

            <div class="campo">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="<?= val($v, 'email') ?>" placeholder="correo@ejemplo.com">
                <?= err($errores, 'email') ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" class="btn btn-rojo">
                <?= $esEditar ? '💾 Guardar cambios' : '+ Registrar cliente' ?>
            </button>
            <a href="index.php?modulo=clientes" class="btn btn-gris">Cancelar</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../shared/footer.php'; ?>
