<?php
class Connection {

    private string $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";

    private array $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    public function connect(): PDO {
        try {
            return new PDO($this->dsn, DB_USER, DB_PASS, $this->options);
        } catch (PDOException $e) {
            error_log('[DB] Connection failed: ' . $e->getMessage());
            Response::error('Database connection failed', 500);
            exit();
        }
    }
}