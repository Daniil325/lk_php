<?php

namespace Infrastructure;

require_once __DIR__ . "/../Interfaces.php";

use Infrastructure\IUserRepository;
use Exception;

class FileRepo implements IUserRepository
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = __DIR__ . "../../../../users.txt";
    }

    public function getById(int $id): ?array
    {
        
    }

    public function getByEmail(string $email): array | null
    {
        error_log("FILE REPO" . $email);
        if (!file_exists($this->filePath)) {
            error_log("File not found: $this->filePath");
            return null;
        }

        // Читаем файл построчно
        $lines = file($this->filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (count($parts) === 2 && trim($parts[0]) === $email) {
                return ['email' => $email, 'password' => trim($parts[1])];
            }
        }

        // Если email не найден
        error_log("Email not found: $email");
        return null;
    }
}
