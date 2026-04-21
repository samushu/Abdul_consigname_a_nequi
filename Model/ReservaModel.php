<?php
// Model/ReservaModel.php

require_once __DIR__ . '/Database.php';

class ReservaModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── Listar todas con detalle ───────────────────────────
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT r.*,
                    c.nombre       AS cliente_nombre,
                    c.documento    AS cliente_documento,
                    v.marca        AS vehiculo_marca,
                    v.modelo       AS vehiculo_modelo,
                    v.placa        AS vehiculo_placa,
                    v.categoria    AS vehiculo_categoria
             FROM reservas r
             JOIN clientes  c ON r.cliente_id  = c.id
             JOIN vehiculos v ON r.vehiculo_id = v.id
             ORDER BY r.fecha_inicio DESC"
        );
        return $stmt->fetchAll();
    }

    // ── Buscar por ID ──────────────────────────────────────
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT r.*,
                    c.nombre    AS cliente_nombre,
                    v.marca     AS vehiculo_marca,
                    v.modelo    AS vehiculo_modelo,
                    v.placa     AS vehiculo_placa,
                    v.precio_dia
             FROM reservas r
             JOIN clientes  c ON r.cliente_id  = c.id
             JOIN vehiculos v ON r.vehiculo_id = v.id
             WHERE r.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ── Crear reserva (y marcar vehículo como alquilado) ───
    public function create(array $data): bool {
        $this->db->beginTransaction();
        try {
            // Calcular total
            $dias  = (new DateTime($data['fecha_fin']))
                         ->diff(new DateTime($data['fecha_inicio']))->days + 1;
            $total = $dias * (float)$data['precio_dia'];

            $stmt = $this->db->prepare(
                "INSERT INTO reservas (cliente_id, vehiculo_id, fecha_inicio, fecha_fin, total, estado)
                 VALUES (:cliente_id, :vehiculo_id, :fecha_inicio, :fecha_fin, :total, 'ACTIVA')"
            );
            $stmt->execute([
                ':cliente_id'   => $data['cliente_id'],
                ':vehiculo_id'  => $data['vehiculo_id'],
                ':fecha_inicio' => $data['fecha_inicio'],
                ':fecha_fin'    => $data['fecha_fin'],
                ':total'        => $total,
            ]);

            // Marcar vehículo como ALQUILADO
            $upd = $this->db->prepare(
                "UPDATE vehiculos SET estado = 'ALQUILADO' WHERE id = :id"
            );
            $upd->execute([':id' => $data['vehiculo_id']]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // ── Finalizar / cancelar reserva ──────────────────────
    public function cambiarEstado(int $id, string $nuevoEstado): bool {
        $reserva = $this->getById($id);
        if (!$reserva) return false;

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                "UPDATE reservas SET estado = :estado WHERE id = :id"
            );
            $stmt->execute([':estado' => $nuevoEstado, ':id' => $id]);

            // Liberar el vehículo si ya no está activo
            if (in_array($nuevoEstado, ['FINALIZADA', 'CANCELADA'])) {
                $upd = $this->db->prepare(
                    "UPDATE vehiculos SET estado = 'DISPONIBLE' WHERE id = :id"
                );
                $upd->execute([':id' => $reserva['vehiculo_id']]);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // ── Verificar conflicto de fechas ──────────────────────
    public function hayConflicto(int $vehiculoId, string $inicio, string $fin, int $excluirId = 0): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservas
             WHERE vehiculo_id = :vid
               AND estado = 'ACTIVA'
               AND id != :excluir
               AND fecha_inicio <= :fin
               AND fecha_fin    >= :inicio"
        );
        $stmt->execute([
            ':vid'     => $vehiculoId,
            ':inicio'  => $inicio,
            ':fin'     => $fin,
            ':excluir' => $excluirId,
        ]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
