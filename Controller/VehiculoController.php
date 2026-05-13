<?php
// Controller/VehiculoController.php

require_once __DIR__ . '/../Model/VehiculoModel.php';

class VehiculoController {

    private VehiculoModel $model;

    public static array $CATEGORIAS = ['AUTOMOVIL', 'CAMIONETA', 'MOTO'];
    public static array $ESTADOS    = ['DISPONIBLE', 'ALQUILADO', 'MANTENIMIENTO'];

    private const UPLOAD_DIR = 'View/assets/img/uploads/';

    public function __construct() {
        $this->model = new VehiculoModel();
    }

    public function index(): array {
        return [
            'vehiculos' => $this->model->getAll(),
            'exito'     => $_GET['exito'] ?? null,
            'error'     => $_GET['error'] ?? null,
        ];
    }

    public function formulario(int $id = 0): array {
        $vehiculo = $id ? $this->model->getById($id) : null;
        return [
            'vehiculo'   => $vehiculo,
            'id'         => $id,
            'categorias' => self::$CATEGORIAS,
            'estados'    => self::$ESTADOS,
        ];
    }

    public function crear(): void {
        $datos        = $this->validar($_POST);
        $errores      = $datos['errores'];
        $nombreImagen = $this->subirImagen($errores);

        if (empty($errores)) {
            $datos['campos']['imagen'] = $nombreImagen;
            $this->model->create($datos['campos'])
                ? header('Location: index.php?modulo=vehiculos&exito=creado')
                : header('Location: index.php?modulo=vehiculos&error=db');
        } else {
            if ($nombreImagen) @unlink(self::UPLOAD_DIR . $nombreImagen);
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header('Location: index.php?modulo=vehiculos&accion=nuevo');
        }
        exit;
    }

    public function editar(int $id): void {
        $datos        = $this->validar($_POST);
        $errores      = $datos['errores'];
        $nombreImagen = $this->subirImagen($errores);

        if (empty($errores)) {
            // Si hay imagen nueva, borrar la anterior del disco
            if ($nombreImagen) {
                $actual = $this->model->getById($id);
                if ($actual && !empty($actual['imagen'])) {
                    @unlink(self::UPLOAD_DIR . $actual['imagen']);
                }
                $datos['campos']['imagen'] = $nombreImagen;
            }
            $this->model->update($id, $datos['campos'])
                ? header('Location: index.php?modulo=vehiculos&exito=editado')
                : header('Location: index.php?modulo=vehiculos&error=db');
        } else {
            if ($nombreImagen) @unlink(self::UPLOAD_DIR . $nombreImagen);
            $_SESSION['form_errores'] = $errores;
            $_SESSION['form_data']    = $_POST;
            header("Location: index.php?modulo=vehiculos&accion=editar&id=$id");
        }
        exit;
    }

    public function eliminar(int $id): void {
        if ($this->model->tieneReservas($id)) {
            header('Location: index.php?modulo=vehiculos&error=reservas_activas');
            exit;
        }
        // Borrar imagen del disco si existe
        $vehiculo = $this->model->getById($id);
        if ($vehiculo && !empty($vehiculo['imagen'])) {
            @unlink(self::UPLOAD_DIR . $vehiculo['imagen']);
        }
        $this->model->delete($id)
            ? header('Location: index.php?modulo=vehiculos&exito=eliminado')
            : header('Location: index.php?modulo=vehiculos&error=db');
        exit;
    }

    // ── Manejo de subida de imagen ─────────────────────────
    private function subirImagen(array &$errores): ?string {
        // Sin archivo — no pasa nada, es opcional
        if (empty($_FILES['imagen']['name'])) return null;

        $f = $_FILES['imagen'];

        if ($f['error'] !== UPLOAD_ERR_OK) {
            $errores['imagen'] = 'Error al subir el archivo.';
            return null;
        }

        // Validar tipo MIME real (no confiar en extensión)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime  = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);

        $permitidos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!array_key_exists($mime, $permitidos)) {
            $errores['imagen'] = 'Solo se aceptan imágenes JPG, PNG o WEBP.';
            return null;
        }

        // Máximo 2 MB
        if ($f['size'] > 2 * 1024 * 1024) {
            $errores['imagen'] = 'La imagen no puede superar 2 MB.';
            return null;
        }

        $nombre  = 'veh_' . uniqid('', true) . '.' . $permitidos[$mime];
        $destino = self::UPLOAD_DIR . $nombre;

        if (!move_uploaded_file($f['tmp_name'], $destino)) {
            $errores['imagen'] = 'No se pudo guardar la imagen. Revisa permisos de la carpeta.';
            return null;
        }

        return $nombre;
    }

    // ── Validación de campos de texto ──────────────────────
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
