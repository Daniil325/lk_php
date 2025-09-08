<?php

namespace Application\Controllers;

use InvalidArgumentException;
use Exception;


class LoginController
{
    private $view;
    private $loginCmd;

    public function __construct($view, $loginCmd)
    {
        $this->view = $view;
        $this->loginCmd = $loginCmd;
    }

    public function displayInfo()
    {
        error_log("LoginController::displayInfo() called");
        $this->view->render("Login");

        $html = ob_get_clean();
        header('Content-Type: text/html');
        echo $html;
    }

    public function loginUser()
    {
        try {
            $res = $this->loginCmd->handle($_POST);
            header('Content-Type: application/json');
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'data' => $res
            ];
        } catch (InvalidArgumentException $e) {
            // Ошибки валидации (400 Bad Request)
            http_response_code(400);
            error_log("Validation error: " . $e->getMessage());

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'error_type' => 'validation'
            ];
        } catch (Exception $e) {
            http_response_code(401);
            error_log("Authentication error: " . $e->getMessage());

            $response = [
                'success' => false,
                'message' => $e->getMessage(),
                'error_type' => 'authentication'
            ];
        }

        echo json_encode($response);
        //session_write_close();
    }
}
