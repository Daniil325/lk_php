<?php

namespace Application\Commands;

use Domain\Entities\UserModel;
use InvalidArgumentException;
use Exception;

class LoginUseCase 
{
    private $userRepo;
    private $sessionRepo;

    public function __construct($userRepo, $sessionRepo)
    {
        $this->userRepo = $userRepo;
        $this->sessionRepo = $sessionRepo;
    }

    public function handle(array $data): string
    {
        $this->validateInput($data);
        $userData = $this->checkUser($data);
        $this->setSession($userData);
        return $userData->id;
    }


    private function validateInput(array $data): void
    {
        if (empty($data['email']) || empty($data['password'])) {
            throw new InvalidArgumentException("Email и пароль обязательны");
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Некорректный формат email");
        }
    }

    // returns user info
    private function checkUser(array $data): UserModel
    {
        $user = $this->userRepo->getByEmail($data['email']);

        if (!$user) {
            throw new Exception("Неправильное имя пользователя или пароль");
        }

        $userObject = UserModel::fromArray($user);

        if (!password_verify($data['password'], $userObject->password)) {
            throw new Exception("Неправильное имя пользователя или пароль");
        }

        return $userObject;
    }

    private function setSession(UserModel $userData): void
    {
        $this->sessionRepo->set("dateIn", date("Y-m-d H:i:s"));
        $this->sessionRepo->set('userData', [
            'id' => $userData->id,
            'email' => $userData->email,
        ]);
    }
}
