<?php

namespace Application\Commands;

use Domain\Entities\SessionModel;
use Domain\Entities\SessionCollection;
use Infrastructure\ISession;

class SessionUseCase
{
    private ISession $sessionRepo;

    function __construct(ISession $sessionRepo)
    {
        $this->sessionRepo = $sessionRepo;
    }

    public function handle($id): SessionCollection
    {
        error_log("SESSION USECASE called");
        $items = $this->sessionRepo->getUserSessions($id);

        error_log("DDDDDDDDDDDDD " . print_r($items, true));
        
        $sessions = [];

        foreach ($items as $item) {
            
            $sessions[] = SessionModel::fromArray($item);
        }
        
        return new SessionCollection($sessions);
    }
}
