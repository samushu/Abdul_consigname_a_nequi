<?php
// Controller/VehiculoController.php

require_once __DIR__ . '/../Model/VehiculoModel.php';

class VehiculoController {

    private VehiculoModel $model;

    public static array $CATEGORIAS = ['AUTOMOVIL', 'CAMIONETA', 'MOTO'];
    public static array $ESTADOS    = ['DISPONIBLE', 'ALQUILADO', 'MANTENIMIENTO'];

    public function __construct() {
        $this->model = new VehiculoModel();
    }

    // ── Listado ────────────────────────────────────────────
    public function index(): array {
        return [
            'vehiculos' => $this->model->getAll(),
            'exito'     => $_GET['exito'] ?? null,
            'error'     => $_GET['error'] ?? null,
        ];
    }

    // ── Formulario crear / editar ──────────────────────────
    public function formulario(int $id = 0): array {
        $vehiculo = $id ? $this->model->getById($id) : null;
        return [
            'vehiculo'   => $vehiculo,
            'id'         => $id,
            'categorias' => self::$CATEGORIAS,
            'estados'    => self::$ESTADOS,
        ];
    }

    // ── Procesar POST crear ────────────────────────────────
    public function crear(): void {
        $datos   = $this->validar($_POST);
        $errores = $datos['errores'];

        if (empty($errores)) {
            $this->model->create($datos['campos'])
                ? header('Location: index.php?modulo=vehiculos&exito=creado')
                : header('Location: index.php?modulo=vehiculos&error=db');
        } else {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header('Location: index.php?modulo=vehiculos&accion=nuevo');
        }
        exit;
    }

    // ── Procesar POST editar ───────────────────────────────
    public function editar(int $id): void {
        $datos   = $this->validar($_POST);
        $errores = $datos['errores'];

        if (empty($errores)) {
            $this->model->update($id, $datos['campos'])
                ? header('Location: index.php?modulo=vehiculos&exito=editado')
                : header('Location: index.php?modulo=vehiculos&error=db');
        } else {
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header("Location: index.php?modulo=vehiculos&accion=editar&id=$id");
        }
        exit;
    }

    // ── Eliminar ───────────────────────────────────────────
    public function eliminar(int $id): void {
        if ($this->model->tieneReservas($id)) {
            header('Location: index.php?modulo=vehiculos&error=reservas_activas');
            exit;
        }
        $this->model->delete($id)
            ? header('Location: index.php?modulo=vehiculos&exito=eliminado')
            : header('Location: index.php?modulo=vehiculos&error=db');
        exit;
    }

    // ── Validación interna ─────────────────────────────────
    private function validar(array $post): array {
        $errores = [];
        $campos  = [];

        $campos['marca'] = trim($post['marca'] ?? '');
        if (empty($campos['marca']))
            $errores['marca'] = 'La marca es obligatoria.';

        $campos['modelo'] = trim($post['modelo'] ?? '');
        if (empty($campos['modelo']))
            $errores['modelo'] = 'El modelo es obligatorio.';

        $campos['anio'] = (int)($post['anio'] ?? 0);
        if ($campos['anio'] < 1990 || $campos['anio'] > (int)date('Y') + 1)
            $errores['anio'] = 'El año no es válido.';

        $campos['placa'] = trim($post['placa'] ?? '');
        if (empty($campos['placa']))
            $errores['placa'] = 'La placa es obligatoria.';

        $campos['categoria'] = $post['categoria'] ?? '';
        if (!in_array($campos['categoria'], self::$CATEGORIAS))
            $errores['categoria'] = 'Categoría no válida.';

        $campos['estado'] = $post['estado'] ?? '';
        if (!in_array($campos['estado'], self::$ESTADOS))
            $errores['estado'] = 'Estado no válido.';

        $campos['precio_dia'] = (float)str_replace(',', '.', $post['precio_dia'] ?? '0');
        if ($campos['precio_dia'] <= 0)
            $errores['precio_dia'] = 'El precio debe ser mayor a 0.';

        return ['campos' => $campos, 'errores' => $errores];
    }
}
