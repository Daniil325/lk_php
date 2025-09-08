<?php

namespace Application;

use Core\DIContainer;
use Application\Commands\RegistrationUseCase;
use Application\Commands\LoginUseCase;
use Application\Commands\ProfileUseCase;
use Application\Commands\SessionUseCase;
use Application\Commands\LogoutCommand;
use Application\Commands\UploadPhotoCommand;
use Application\Controllers\RegistrationController;
use Application\Controllers\LoginController;
use Application\Controllers\ProfileController;
use Application\Controllers\SessionController;
use Presentation\View\LoginView;
use Presentation\View\RegistrationView;
use Infrastructure\IUserRepository;
use Infrastructure\ISession;
use Infrastructure\Database\ImageRepository;
use Infrastructure\IProfileRepository;
use Presentation\View\BaseView;


class ApplicationFactory
{
    private DIContainer $container;

    public function __construct(DIContainer $container)
    {
        $this->container = $container;
        $this->registerUseCases();
        $this->registerControllers();
    }

    private function registerUseCases(): void
    {
        $this->container->set(RegistrationUseCase::class, function () {
            return new RegistrationUseCase($this->container->get(IUserRepository::class), $this->container->get(ISession::class));
        });

        $this->container->set(LoginUseCase::class, function () {
            return new LoginUseCase(
                $this->container->get(IUserRepository::class),
                $this->container->get(ISession::class)
            );
        });

        $this->container->set(UploadPhotoCommand::class, function () {
            return new UploadPhotoCommand($this->container->get(ImageRepository::class));
        });

        $this->container->set(ProfileUseCase::class, function () {
            return new ProfileUseCase(
                $this->container->get(IProfileRepository::class),
            );
        });

        $this->container->set(SessionUseCase::class, function () {
            return new SessionUseCase(
                $this->container->get(ISession::class),
            );
        });

        $this->container->set(LogoutCommand::class, function () {
            return new LogoutCommand(
                $this->container->get(ISession::class),
            );
        });
    }

    private function registerControllers(): void
    {
        $this->container->set(RegistrationController::class, function () {
            $view = new RegistrationView();
            $cmdReg = $this->container->get(RegistrationUseCase::class);
            $cmdImg = $this->container->get(UploadPhotoCommand::class);
            return new RegistrationController($view, $cmdReg, $cmdImg);
        });

        $this->container->set(LoginController::class, function () {
            $view = new LoginView();
            $cmd = $this->container->get(LoginUseCase::class);
            return new LoginController($view, $cmd);
        });

        $this->container->set(ProfileController::class, function () {
            $view = new BaseView();
            $cmd = $this->container->get(ProfileUseCase::class);
            return new ProfileController($view, $cmd);
        });

        $this->container->set(SessionController::class, function () {
            $view = new BaseView();
            $cmd = $this->container->get(SessionUseCase::class);
            return new SessionController($view, $cmd);
        });
    }
}
