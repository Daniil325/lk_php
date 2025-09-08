<?php

namespace Infrastructure;

interface IProfileRepository 
{
    public function getById(int $id): array | null;
}

interface ISqlRepository
{
    public function getById(int $id): array | null;

    public function add(array $data): int;
}

interface IUserRepository extends IProfileRepository
{
    public function getByEmail(string $email);
}

interface IPhotoRepository
{
    public function getByName(string $name);

    public function upload();
}

interface ISession
{
    public function start(): bool;

    public function set(string $key, $value): void;

    public function get(string $key, $default = null);

    public function has(string $key): bool;

    public function remove(string $key): void;

    public function destroy(?string $id): void;

    public function regenerateId(bool $deleteOldSession = true): bool;

    public function getUserSessions(int $userId): array;
}
