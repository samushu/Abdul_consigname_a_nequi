<?php
// Controller/ClienteController.php

require_once __DIR__ . '/../Model/ClienteModel.php';

class ClienteController {

    private ClienteModel $model;

    public function __construct() {
        $this->model = new ClienteModel();
    }

    // ── Listado ────────────────────────────────────────────
    public function index(): array {
        return [
            'clientes' => $this->model->getAll(),
            'exito'    => $_GET['exito']  ?? null,
            'error'    => $_GET['error']  ?? null,
        ];
    }

    // ── Formulario crear / editar ──────────────────────────
    public function formulario(int $id = 0): array {
        $cliente = $id ? $this->model->getById($id) : null;
        return ['cliente' => $cliente, 'id' => $id];
    }

    // ── Procesar POST crear ────────────────────────────────
    public function crear(): void {
        $datos  = $this->validar($_POST);
        $errores = $datos['errores'];

        if (empty($errores)) {
            if ($this->model->create($datos['campos'])) {
                header('Location: index.php?modulo=clientes&exito=creado');
            } else {
                header('Location: index.php?modulo=clientes&error=db');
            }
        } else {
            // Volver al formulario con errores
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header('Location: index.php?modulo=clientes&accion=nuevo');
        }
        exit;
    }

    // ── Procesar POST editar ───────────────────────────────
    public function editar(int $id): void {
        $datos   = $this->validar($_POST);
        $errores = $datos['errores'];

        if (empty($errores)) {
            if ($this->model->update($id, $datos['campos'])) {
                header('Location: index.php?modulo=clientes&exito=editado');
            } else {
                header('Location: index.php?modulo=clientes&error=db');
            }
        } else {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header("Location: index.php?modulo=clientes&accion=editar&id=$id");
        }
        exit;
    }

    // ── Eliminar ───────────────────────────────────────────
    public function eliminar(int $id): void {
        if ($this->model->tieneReservas($id)) {
            header('Location: index.php?modulo=clientes&error=reservas_activas');
            exit;
        }
        $this->model->delete($id)
            ? header('Location: index.php?modulo=clientes&exito=eliminado')
            : header('Location: index.php?modulo=clientes&error=db');
        exit;
    }

    // ── Validación interna ─────────────────────────────────
    private function validar(array $post): array {
        $errores = [];
        $campos  = [];

        $campos['nombre'] = trim($post['nombre'] ?? '');
        if (empty($campos['nombre']))
            $errores['nombre'] = 'El nombre es obligatorio.';

        $campos['documento'] = trim($post['documento'] ?? '');
        if (empty($campos['documento']))
            $errores['documento'] = 'El documento es obligatorio.';

        $campos['telefono'] = trim($post['telefono'] ?? '');
        $campos['email']    = trim($post['email']    ?? '');

        if (!empty($campos['email']) && !filter_var($campos['email'], FILTER_VALIDATE_EMAIL))
            $errores['email'] = 'El correo no tiene un formato válido.';

        $campos['licencia'] = trim($post['licencia'] ?? '');
        if (empty($campos['licencia']))
            $errores['licencia'] = 'La licencia es obligatoria.';

        return ['campos' => $campos, 'errores' => $errores];
    }
}
