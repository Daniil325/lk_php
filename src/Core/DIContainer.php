<?php

namespace Core;

require_once "Singleton.php";

use Core\Singleton;
use Exception;

class DIContainer extends Singleton
{
    private $dependencies = [];

    private function __construct(array $dependencies = [])
    {
        $this->dependencies = $dependencies;
        error_log("DIContainer initialized");
    }

    public static function instance(array $dependencies = []): self
    {
        if (null === self::$instance) {
            self::$instance = new self($dependencies);
        }
        return self::$instance;
    }

    public function has(string $id): bool
    {
        return isset($this->dependencies[$id]);
    }

    public function get(string $id)
    {
        if ($this->has($id)) {
            try {
                return $this->resolve($id);
            } catch (Exception $e) {
                error_log("Error resolving dependency $id: " . $e->getMessage());
                throw $e; // Перебрасываем исключение
            }
        }
        throw new Exception("Dependency $id not found.");
    }

    public function make(string $id)
    {
        try {
            return $this->get($id);
        } catch (Exception $e) {
            error_log("Failed to make dependency $id: " . $e->getMessage());
            throw new Exception("Cannot create instance of $id: " . $e->getMessage());
        }
    }

    private function resolve(string $id)
    {
        return call_user_func($this->dependencies[$id], $this);
    }

    public function set(string $id, callable $factory): void
    {
        $this->dependencies[$id] = $factory;
    }
}