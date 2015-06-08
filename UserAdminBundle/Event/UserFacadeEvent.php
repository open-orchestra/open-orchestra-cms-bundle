<?php

namespace OpenOrchestra\UserAdminBundle\Event;

use OpenOrchestra\UserAdminBundle\Facade\UserFacade;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class UserFacadeEvent
 */
class UserFacadeEvent extends Event
{
    protected $userFacade;

    /**
     * @param UserFacade $userFacade
     */
    public function __construct(UserFacade $userFacade)
    {
        $this->userFacade = $userFacade;
    }

    /**
     * @return UserFacade
     */
    public function getUserFacade()
    {
        return $this->userFacade;
    }
}
