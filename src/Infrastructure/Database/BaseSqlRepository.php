<?php

namespace Infrastructure\Database;

use Infrastructure\ISqlRepository;
use Exception;

class BaseSqlRepository implements ISqlRepository
{
    protected $session;
    protected string $tableName;

    function __construct($session, string $tableName)
    {
        $this->session = $session->getConnection();
        $this->tableName = $tableName;
    }

    public function add(array $data, array $types = []): int
    {
        unset($data['id']);
        unset($types[array_search('id', array_keys($data))]);

        $columns = implode(", ", array_keys($data));

        $placeholders = implode(", ", array_fill(0, count($data), "?"));
        $sql = "INSERT INTO {$this->tableName} ($columns) VALUES ($placeholders)";

        $stmt = $this->session->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->session->error);
        }

        // Determine parameter types
        if (empty($types)) {
            // Default to string type if not specified
            $types = array_fill(0, count($data), "s");
        } elseif (count($types) !== count($data)) {
            throw new Exception("Number of types (" . count($types) . ") does not match number of parameters (" . count($data) . ")");
        }

        // Get values
        $values = array_values($data);
        if (empty($values)) {
            throw new Exception("No data supplied for parameters in prepared statement");
        }

        // Bind parameters
        $stmt->bind_param(implode("", $types), ...$values);

        if ($stmt->execute()) {
            $lastId = $this->session->insert_id;
            $stmt->close();
            return $lastId;
        } else {
            $error = $stmt->error;
            $stmt->close();
            throw new Exception("Insert failed: " . $error);
        }
    }

    public function getById(int $id): array | null
    {
        error_log("GET BY ID FROM $this->tableName");
        $stmt = $this->session->prepare("SELECT * FROM $this->tableName WHERE id = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();

        error_log("BY ID " . print_r($data, true));
        return $data;
    }
}
