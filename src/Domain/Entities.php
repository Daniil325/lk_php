<?php

namespace Domain\Entities;

interface IModel
{
    public static function fromArray(array $data): self;
}

class RegisterUserModel implements IModel
{
    public string $email;
    public string $name;
    public string $surname;
    public string $photoData;
    public int $age;
    public string $sex;
    public string $password;

    public function __construct($email, $name, $surname, $photoData, $age, $sex, $password)
    {
        $this->email = $email;
        $this->name = $name;
        $this->surname = $surname;
        $this->photoData = $photoData;
        $this->age = $age;
        $this->sex = $sex;
        $this->password = $password;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['email'] ?? '',
            $data['name'] ?? '',
            $data['surname'] ?? '',
            $data['photoData'] ?? '',
            $data['age'] ?? 0,
            $data['sex'] ?? 'm',
            $data['password'] ?? '',
        );
    }
}

class UserModel extends RegisterUserModel
{
    public int $id;

    public function __construct($id, $email, $name, $surname, $photoData, $age, $sex, $password)
    {
        parent::__construct($email, $name, $surname, $photoData, $age, $sex, $password);
        $this->id = $id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['email'] ?? '',
            $data['name'] ?? '',
            $data['surname'] ?? '',
            $data['photoData'] ?? '',
            $data['age'] ?? 0,
            $data['sex'] ?? 'm',
            $data['password'] ?? '',
        );
    }
}

class SessionData
{
    public $dateIn;
    public $userData; // user id and email

    public function __construct(string $dateIn, array $userData)
    {
        $this->dateIn = $dateIn;
        $this->userData = $userData;
    }
}

class SessionModel implements IModel
{
    public string $id;
    public SessionData $data;
    public int $timestamp;

    public function __construct(string $id, SessionData $data, int $timestamp)
    {
        $this->id = $id;
        $this->data = $data;
        $this->timestamp = $timestamp;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            new SessionData($data['data']['dateIn'], $data['data']['userData']), 
            $data['timestamp']
        );
    }
}

class SessionCollection implements \IteratorAggregate
{
    private array $sessions = [];

    public function __construct(array $sessions = [])
    {
        foreach ($sessions as $session) {
            $this->add($session);
        }
    }

    public function add(SessionModel $session): void
    {
        $this->sessions[] = $session;
    }

    public function count(): int
    {
        return count($this->sessions);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->sessions);
    }
}
