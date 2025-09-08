<?php

namespace Application\Controllers;

use Application\Commands\BaseUserCommand;
use Exception;


class RegistrationController
{
    private $view;
    private $registerUserCmd;
    private $uploadPhotoCmd;

    public function __construct($view, $registerUserCmd, $uploadPhotoCmd)
    {
        $this->view = $view;
        $this->registerUserCmd = $registerUserCmd;
        $this->uploadPhotoCmd = $uploadPhotoCmd;
    }

    public function displayInfo()
    {
        $this->view->render("registration");

        $html = ob_get_clean();
        // Устанавливаем заголовок для HTML
        header('Content-Type: text/html');
        // Возвращаем HTML
        echo $html;
    }

    public function registerUser()
    {
        error_log("RegistrationController::registerUser() called");
        try {
            $userData = $_POST;

            $imgId = $this->uploadPhotoCmd->handle($_FILES["profilePhoto"]);
            $userData["profilePhoto"] = $imgId;
            $res = $this->registerUserCmd->handle($userData);
            header('Content-Type: application/json');

            $response = [
                'success' => true,
                'message' => 'Login successful',
                'data' => $res
            ];
        } catch (Exception $e) {
            http_response_code(401);
            error_log("Authentication error: " . $e->getMessage());

            $response = [
                'success' => false,
                'message' => "Пользователь с таким Email уже существует",
                'error_type' => 'authentication'
            ];
        }

        echo json_encode($response);
    }
}
