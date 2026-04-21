<?php
// Controller/ReservaController.php

require_once __DIR__ . '/../Model/ReservaModel.php';
require_once __DIR__ . '/../Model/ClienteModel.php';
require_once __DIR__ . '/../Model/VehiculoModel.php';

class ReservaController {

    private ReservaModel  $model;
    private ClienteModel  $clientes;
    private VehiculoModel $vehiculos;

    public function __construct() {
        $this->model     = new ReservaModel();
        $this->clientes  = new ClienteModel();
        $this->vehiculos = new VehiculoModel();
    }

    // ── Listado ────────────────────────────────────────────
    public function index(): array {
        return [
            'reservas' => $this->model->getAll(),
            'exito'    => $_GET['exito'] ?? null,
            'error'    => $_GET['error'] ?? null,
        ];
    }

    // ── Formulario nueva reserva ───────────────────────────
    public function formulario(): array {
        return [
            'clientes'   => $this->clientes->getAll(),
            'vehiculos'  => $this->vehiculos->getDisponibles(),
            'hoy'        => date('Y-m-d'),
        ];
    }

    // ── Procesar POST crear ────────────────────────────────
    public function crear(): void {
        $datos   = $this->validar($_POST);
        $errores = $datos['errores'];

        if (empty($errores)) {
            // Verificar conflicto de fechas
            if ($this->model->hayConflicto(
                    (int)$datos['campos']['vehiculo_id'],
                    $datos['campos']['fecha_inicio'],
                    $datos['campos']['fecha_fin']
                )) {
                $errores['fecha'] = 'El vehículo ya tiene una reserva activa en esas fechas.';
            }
        }

        if (empty($errores)) {
            // Adjuntar precio_dia del vehículo
            $v = $this->vehiculos->getById((int)$datos['campos']['vehiculo_id']);
            $datos['campos']['precio_dia'] = $v['precio_dia'];

            $this->model->create($datos['campos'])
                ? header('Location: index.php?modulo=reservas&exito=creada')
                : header('Location: index.php?modulo=reservas&error=db');
        } else {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header('Location: index.php?modulo=reservas&accion=nuevo');
        }
        exit;
    }

    // ── Finalizar / cancelar reserva ──────────────────────
    public function cambiarEstado(int $id, string $estado): void {
        $this->model->cambiarEstado($id, $estado)
            ? header('Location: index.php?modulo=reservas&exito=' . strtolower($estado))
            : header('Location: index.php?modulo=reservas&error=db');
        exit;
    }

    // ── Validación interna ─────────────────────────────────
    private function validar(array $post): array {
        $errores = [];
        $campos  = [];

        $campos['cliente_id'] = (int)($post['cliente_id'] ?? 0);
        if ($campos['cliente_id'] <= 0)
            $errores['cliente_id'] = 'Selecciona un cliente.';

        $campos['vehiculo_id'] = (int)($post['vehiculo_id'] ?? 0);
        if ($campos['vehiculo_id'] <= 0)
            $errores['vehiculo_id'] = 'Selecciona un vehículo.';

        $campos['fecha_inicio'] = trim($post['fecha_inicio'] ?? '');
        $campos['fecha_fin']    = trim($post['fecha_fin']    ?? '');

        if (empty($campos['fecha_inicio']))
            $errores['fecha_inicio'] = 'La fecha de inicio es obligatoria.';
        if (empty($campos['fecha_fin']))
            $errores['fecha_fin'] = 'La fecha de fin es obligatoria.';

        if (empty($errores['fecha_inicio']) && empty($errores['fecha_fin'])) {
            if ($campos['fecha_fin'] < $campos['fecha_inicio'])
                $errores['fecha_fin'] = 'La fecha de fin debe ser igual o posterior a la de inicio.';
        }

        return ['campos' => $campos, 'errores' => $errores];
    }
}
