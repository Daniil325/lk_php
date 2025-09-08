<?php

namespace Infrastructure;

use Throwable;
use Core\DIContainer;
use Infrastructure\IUserRepository;
use Infrastructure\IProfileRepository;
use Infrastructure\Database\SqlUserRepository;
use Infrastructure\Database\DbSession;
use Infrastructure\Database\ImageRepository;
use Infrastructure\Database\DatabaseSession;
use Infrastructure\FileRepo;
use Infrastructure\Database\ProfileRepository;
use Infrastructure\File\FileSession;

class InfraFactory
{
    private DIContainer $container;
    private string $authMethod;
    private string $sessionType;
    private array $dbSettings;

    public function __construct(DIContainer $container, array $config)
    {
        $this->container = $container;
        $this->authMethod = $config["authMethod"];
        $this->sessionType = $config["sessionType"];
        $this->dbSettings = $config["db"];
        $this->registerDbSession();
        $this->registerRepos();
        $this->registerSessionRepo();
    }

    private function registerRepos(): void
    {
        $this->container->set(IProfileRepository::class, function () {
            switch ($this->authMethod) {
                case "file":
                    return new FileRepo();
                    break;
                case "db":
                    return new ProfileRepository($this->container->get(DbSession::class));
                    break;
                default:
                    throw new Throwable("Method not allowed");
                    break;
            }
        });
        $this->container->set(IUserRepository::class, function ($container) {
            switch ($this->authMethod) {
                case "file":
                    return new FileRepo();
                    break;
                case "db":
                    return new SqlUserRepository($this->container->get(DbSession::class));
                    break;
                default:
                    throw new Throwable("Method not allowed");
                    break;
            }
        });

        $this->container->set(ImageRepository::class, function ($container) {
            return new ImageRepository($this->container->get(DbSession::class));
        });
    }

    private function registerDbSession(): void
    {
        $this->container->set(DbSession::class, function ($container) {
            return new DbSession($this->dbSettings);
        });
    }

    private function registerSessionRepo(): void
    {
        $this->container->set(ISession::class, function ($container) {
            switch ($this->sessionType) {
                case "file":
                    return new FileSession();
                    break;
                case "db":
                    return new DatabaseSession($this->container->get(DbSession::class));
                    break;
                default:
                    throw new \Exception("Session method '{$this->sessionType}' not allowed");
                    break;
            }
        });
    }
}
