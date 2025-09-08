<?php

namespace Application\Commands;

use Domain\Entities\UserModel;
use Infrastructure\ISqlRepository;

class ProfileUseCase
{
    private ISqlRepository $userRepo;

    function __construct(ISqlRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function handle($id): UserModel
    {
        error_log("PROFILE USECASE called");
        $userArray = $this->userRepo->getById($id);
        $userData = UserModel::fromArray($userArray);
        return $userData;
    }
}
