<?php

namespace Application\Commands;

use Exception;

class LogoutCommand
{
    private $sessionRepo;

    function __construct($sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    public function handle()
    {
        error_log("LOGOUT COMMAND handle");
        $this->sessionRepo->start();
        $sessionId = session_id();
        $this->sessionRepo->destroy($sessionId);
    }
}
