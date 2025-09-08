<?php

declare(strict_types=1);

namespace Infrastructure\Database;

use Core\Singleton;
use mysqli;
use Exception;

class DbSession extends Singleton
{
    private $conn;

    public function __construct(array $settings)
    {
        try {
            $this->conn = new mysqli($settings['hostname'], $settings['user'], $settings['password'], $settings['database']);
        } catch (Exception $e) {
            error_log("Error connect to db: " . $e->getMessage());
            throw new Exception("Не удалось подключиться к базе данных.");
        }
    }

    public function getConnection(): mysqli
    {
        if ($this->conn === null) {
            throw new Exception("MySQLi не инициализирован.");
        }
        return $this->conn;
    }

    public function closeConnection(): void
    {
        $this->conn = null;
        self::$instance = null; // Сбрасываем Singleton для возможности нового подключения
    }

    public static function getInstance(): DbSession
    {
        return parent::getInstance();
    }
}
