<?php

namespace Application\Controllers;

use Exception;

class SessionController
{
    private $view;
    private $sessionCmd;

    public function __construct($view, $sessionCmd)
    {
        $this->view = $view;
        $this->sessionCmd = $sessionCmd;
    }

    public function displayInfo()
    {
        error_log("SessionController called");
        $id = $this->validateProfileId();
        $data = $this->sessionCmd->handle($id);
        
        $this->view->render("Session", ['data' => $data]);
        $html = ob_get_clean();
        header('Content-Type: text/html');
        echo $html;
    }

    private function validateProfileId()
    {
        $id = $_GET['id'] ?? null;

        if (!$id) {
            throw new Exception('Profile ID is required', 400);
        }

        if (!is_numeric($id)) {
            throw new Exception('Profile ID must be numeric', 400);
        }

        $profileId = (int)$id;

        if ($profileId <= 0) {
            throw new Exception('Profile ID must be positive', 400);
        }

        return $profileId;
    }
}
