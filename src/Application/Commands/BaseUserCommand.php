<?php

namespace Application\Commands;

use Domain\Entities\UserModel;

class BaseUserCommand
{
    private $sessionRepo;

    public function __construct($sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    protected function setSession(UserModel $userData): void
    {
        $this->sessionRepo->set("dateIn", date("Y-m-d H:i:s"));
        $this->sessionRepo->set('userData', [
            'id' => $userData->id,
            'email' => $userData->email,
        ]);
    }
}
