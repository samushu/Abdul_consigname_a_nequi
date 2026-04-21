<?php
// Model/VehiculoModel.php

require_once __DIR__ . '/Database.php';

class VehiculoModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── Listar todos ───────────────────────────────────────
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT * FROM vehiculos ORDER BY marca, modelo ASC"
        );
        return $stmt->fetchAll();
    }

    // ── Solo disponibles ───────────────────────────────────
    public function getDisponibles(): array {
        $stmt = $this->db->query(
            "SELECT * FROM vehiculos WHERE estado = 'DISPONIBLE'
             ORDER BY categoria, marca ASC"
        );
        return $stmt->fetchAll();
    }

    // ── Buscar por ID ──────────────────────────────────────
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM vehiculos WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ── Insertar ───────────────────────────────────────────
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO vehiculos (marca, modelo, anio, placa, categoria, estado, precio_dia)
             VALUES (:marca, :modelo, :anio, :placa, :categoria, :estado, :precio_dia)"
        );
        return $stmt->execute([
            ':marca'      => $data['marca'],
            ':modelo'     => $data['modelo'],
            ':anio'       => $data['anio'],
            ':placa'      => strtoupper($data['placa']),
            ':categoria'  => $data['categoria'],
            ':estado'     => $data['estado'],
            ':precio_dia' => $data['precio_dia'],
        ]);
    }

    // ── Actualizar ─────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE vehiculos
             SET marca=:marca, modelo=:modelo, anio=:anio,
                 placa=:placa, categoria=:categoria,
                 estado=:estado, precio_dia=:precio_dia
             WHERE id=:id"
        );
        return $stmt->execute([
            ':marca'      => $data['marca'],
            ':modelo'     => $data['modelo'],
            ':anio'       => $data['anio'],
            ':placa'      => strtoupper($data['placa']),
            ':categoria'  => $data['categoria'],
            ':estado'     => $data['estado'],
            ':precio_dia' => $data['precio_dia'],
            ':id'         => $id,
        ]);
    }

    // ── Cambiar solo el estado ─────────────────────────────
    public function cambiarEstado(int $id, string $estado): bool {
        $stmt = $this->db->prepare(
            "UPDATE vehiculos SET estado = :estado WHERE id = :id"
        );
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }

    // ── Eliminar ───────────────────────────────────────────
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM vehiculos WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ── ¿Tiene reservas activas? ───────────────────────────
    public function tieneReservas(int $id): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservas
             WHERE vehiculo_id = :id AND estado = 'ACTIVA'"
        );
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
