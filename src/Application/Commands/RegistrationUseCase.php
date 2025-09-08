<?php

namespace Application\Commands;

use Domain\Entities\UserModel;
use Exception;

require "ICommand.php";


class RegistrationUseCase extends BaseUserCommand implements ICommand
{
    public $userRepo;

    public function __construct($userRepo, $sessionRepo)
    {
        parent::__construct($sessionRepo);
        $this->userRepo = $userRepo;
    }

    public function handle($data)
    {
        $user = $this->userRepo->getByEmail($data["email"]);
        if ($user) {
            throw new Exception("Пользователь с таким Email уже существует");
        }
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $id = $this->userRepo->add($data);
        $data["id"] = $id;
        $userData = UserModel::fromArray($data);
        $this->setSession($userData);
        return $id;
    }
}
