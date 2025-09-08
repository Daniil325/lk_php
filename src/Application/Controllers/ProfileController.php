<?php

namespace Application\Controllers;

use Exception;

class ProfileController
{
    private $view;
    private $profileCmd;

    public function __construct($view, $profileCmd)
    {
        $this->view = $view;
        $this->profileCmd = $profileCmd;
    }

    public function displayInfo()
    {
        error_log("ProfileController called");
        $id = $this->validateProfileId();
        $data = $this->profileCmd->handle($id);
        $data->photoData = base64_encode($data->photoData);
        
        $this->view->render("Profile", ['data' => $data]);
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
