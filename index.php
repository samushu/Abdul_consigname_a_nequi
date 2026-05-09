<?php
// index.php  ·  Front Controller · RumBoss MVC
session_start();
 
// ── Autoload de Controllers y Models ──────────
spl_autoload_register(function (string $clase) {
    $rutas = [
        __DIR__ . "/Controller/{$clase}.php",
        __DIR__ . "/Model/{$clase}.php",
    ];
    foreach ($rutas as $ruta) {
        if (file_exists($ruta)) { require_once $ruta; return; }
    }
});
 
// ── Parámetros de ruta ────────────────────────
$modulo = $_GET['modulo'] ?? '';
$accion = $_GET['accion'] ?? 'lista';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
// ── HOME — sin módulo, mostrar landing ────────
if ($modulo === '') { ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RumBoss · Gestor de Alquiler</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="View/css/estilos.css">
</head>
<body>
 
<nav class="navbar">
    <a href="index.php" class="navbar-brand"><span>Rum</span>Boss</a>
    <div class="nav-links">
        <a href="index.php?modulo=vehiculos">Vehículos</a>
        <a href="index.php?modulo=clientes">Clientes</a>
        <a href="index.php?modulo=reservas">Reservas</a>
    </div>
</nav>
 
<section class="hero">
    <p class="hero-tagline">Sistema de gestión</p>
    <h1 class="hero-logo"><span>Rum</span>Boss</h1>
    <div class="hero-divider"></div>
    <p class="hero-tagline">Administra tu flota de vehículos, tus clientes<br>y todas tus reservas desde un solo lugar.</p>
 
    <div class="modulos-grid">
 
        <a href="index.php?modulo=vehiculos" class="modulo-card">
            <div class="modulo-icon">🚗</div>
            <h2>Vehículos</h2>
            <p>Registra y gestiona toda tu flota: automóviles, camionetas y motos. Controla disponibilidad y precios por día.</p>
            <span class="card-cta">Ver vehículos →</span>
        </a>
 
        <a href="index.php?modulo=clientes" class="modulo-card">
            <div class="modulo-icon">👤</div>
            <h2>Clientes</h2>
            <p>Administra la información de tus clientes: documento, licencia de conducir, teléfono y correo electrónico.</p>
            <span class="card-cta">Ver clientes →</span>
        </a>
 
        <a href="index.php?modulo=reservas" class="modulo-card">
            <div class="modulo-icon">📋</div>
            <h2>Reservas</h2>
            <p>Crea y controla reservas activas, finalizadas y canceladas. Calcula el costo total de forma automática.</p>
            <span class="card-cta">Ver reservas →</span>
        </a>
 
    </div>
</section>
 
<footer class="site-footer">
    RumBoss &copy; <?= date('Y') ?> · Gestor de Alquiler de Vehículos
</footer>
 
</body>
</html>
<?php
    exit;
}
 
// ── Tabla de enrutamiento ─────────────────────
$rutas = [
    'clientes' => [
        'lista'   => function() {
            $ctrl = new ClienteController();
            $datos = $ctrl->index();
            require __DIR__ . '/View/clientes/lista.php';
        },
        'nuevo'   => function() {
            $ctrl = new ClienteController();
            $datos = $ctrl->formulario();
            require __DIR__ . '/View/clientes/formulario.php';
        },
        'editar'  => function(int $id) {
            $ctrl = new ClienteController();
            $datos = $ctrl->formulario($id);
            require __DIR__ . '/View/clientes/formulario.php';
        },
        'crear'   => function() {
            (new ClienteController())->crear();
        },
        'guardar' => function(int $id) {
            (new ClienteController())->editar($id);
        },
        'eliminar'=> function(int $id) {
            (new ClienteController())->eliminar($id);
        },
    ],
 
    'vehiculos' => [
        'lista'   => function() {
            $ctrl = new VehiculoController();
            $datos = $ctrl->index();
            require __DIR__ . '/View/vehiculos/lista.php';
        },
        'nuevo'   => function() {
            $ctrl = new VehiculoController();
            $datos = $ctrl->formulario();
            require __DIR__ . '/View/vehiculos/formulario.php';
        },
        'editar'  => function(int $id) {
            $ctrl = new VehiculoController();
            $datos = $ctrl->formulario($id);
            require __DIR__ . '/View/vehiculos/formulario.php';
        },
        'crear'   => function() {
            (new VehiculoController())->crear();
        },
        'guardar' => function(int $id) {
            (new VehiculoController())->editar($id);
        },
        'eliminar'=> function(int $id) {
            (new VehiculoController())->eliminar($id);
        },
    ],
 
    'reservas'  => [
        'lista'     => function() {
            $ctrl = new ReservaController();
            $datos = $ctrl->index();
            require __DIR__ . '/View/reservas/lista.php';
        },
        'nuevo'     => function() {
            $ctrl = new ReservaController();
            $datos = $ctrl->formulario();
            require __DIR__ . '/View/reservas/formulario.php';
        },
        'crear'     => function() {
            (new ReservaController())->crear();
        },
        'finalizar' => function(int $id) {
            (new ReservaController())->cambiarEstado($id, 'FINALIZADA');
        },
        'cancelar'  => function(int $id) {
            (new ReservaController())->cambiarEstado($id, 'CANCELADA');
        },
    ],
];
 
// ── Resolver la acción POST ───────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'editar' && $id > 0) $accion = 'guardar';
    if ($accion === 'nuevo')             $accion  = 'crear';
}
 
// ── Despachar ─────────────────────────────────
if (isset($rutas[$modulo][$accion])) {
    $handler = $rutas[$modulo][$accion];
    $handler($id);
} else {
    $fallback = $rutas[$modulo]['lista'] ?? $rutas['vehiculos']['lista'];
    $fallback(0);
}
