<?php
// Model/Database.php
// Conexión singleton a MySQL

class Database {
    private static ?PDO $conn = null;

    // ── Configuración ──────────────────────────────────────
    private static string $host   = 'localhost';
    private static string $db     = 'rumboss';
    private static string $user   = 'root';
    private static string $pass   = '';          // ajusta según tu entorno
    private static string $charset = 'utf8mb4';

    public static function getConnection(): PDO {
        if (self::$conn === null) {
            $dsn = "mysql:host=" . self::$host
                 . ";dbname=" . self::$db
                 . ";charset=" . self::$charset;

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$conn = new PDO($dsn, self::$user, self::$pass, $options);
            } catch (PDOException $e) {
                // En producción, loguear y mostrar mensaje genérico
                die("<p style='color:red;font-family:sans-serif'>
                     <strong>Error de conexión:</strong> " . htmlspecialchars($e->getMessage()) . "
                     </p>");
            }
        }
        return self::$conn;
    }
}
