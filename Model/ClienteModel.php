<?php
// Model/ClienteModel.php

require_once __DIR__ . '/Database.php';

class ClienteModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // ── Listar todos ───────────────────────────────────────
    public function getAll(): array {
        $stmt = $this->db->query(
            "SELECT * FROM clientes ORDER BY nombre ASC"
        );
        return $stmt->fetchAll();
    }

    // ── Buscar por ID ──────────────────────────────────────
    public function getById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT * FROM clientes WHERE id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ── Insertar ───────────────────────────────────────────
    public function create(array $data): bool {
        $stmt = $this->db->prepare(
            "INSERT INTO clientes (nombre, documento, telefono, email, licencia)
             VALUES (:nombre, :documento, :telefono, :email, :licencia)"
        );
        return $stmt->execute([
            ':nombre'    => $data['nombre'],
            ':documento' => $data['documento'],
            ':telefono'  => $data['telefono'],
            ':email'     => $data['email'],
            ':licencia'  => $data['licencia'],
        ]);
    }

    // ── Actualizar ─────────────────────────────────────────
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare(
            "UPDATE clientes
             SET nombre=:nombre, documento=:documento,
                 telefono=:telefono, email=:email, licencia=:licencia
             WHERE id=:id"
        );
        return $stmt->execute([
            ':nombre'    => $data['nombre'],
            ':documento' => $data['documento'],
            ':telefono'  => $data['telefono'],
            ':email'     => $data['email'],
            ':licencia'  => $data['licencia'],
            ':id'        => $id,
        ]);
    }

    // ── Eliminar ───────────────────────────────────────────
    public function delete(int $id): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM clientes WHERE id = :id"
        );
        return $stmt->execute([':id' => $id]);
    }

    // ── ¿Tiene reservas activas? ───────────────────────────
    public function tieneReservas(int $id): bool {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM reservas
             WHERE cliente_id = :id AND estado = 'ACTIVA'"
        );
        $stmt->execute([':id' => $id]);
        return (int)$stmt->fetchColumn() > 0;
    }
}
