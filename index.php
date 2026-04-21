<?php
// index.php  ·  Front Controller · RumBoss MVC
// ──────────────────────────────────────────────
// Punto de entrada único. Despacha a Controller
// y luego carga la View correspondiente.

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
$modulo = $_GET['modulo'] ?? 'vehiculos';
$accion = $_GET['accion'] ?? 'lista';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

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
// Los formularios hacen POST con accion=crear/editar.
// Para editar con id, mapeamos a 'guardar'.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($accion === 'editar' && $id > 0) $accion = 'guardar';
    if ($accion === 'nuevo')             $accion  = 'crear';
}

// ── Despachar ─────────────────────────────────
if (isset($rutas[$modulo][$accion])) {
    $handler = $rutas[$modulo][$accion];
    $handler($id);
} else {
    // Fallback al listado del módulo o a vehículos
    $fallback = $rutas[$modulo]['lista'] ?? $rutas['vehiculos']['lista'];
    $fallback(0);
}
