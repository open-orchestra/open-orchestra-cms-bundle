<?php

namespace OpenOrchestra\GroupBundle\Event;

use OpenOrchestra\ApiBundle\Facade\GroupFacade;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class GroupFacadeEvent
 */
class GroupFacadeEvent extends Event
{
    protected $groupFacade;
    protected $group;

    /**
     * @param GroupInterface $group
     * @param GroupFacade    $groupFacade
     */
    public function __construct(GroupInterface $group, GroupFacade $groupFacade)
    {
        $this->group = $group;
        $this->groupFacade = $groupFacade;
    }

    /**
     * @return GroupInterface
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return GroupFacade
     */
    public function getGroupFacade()
    {
        return $this->groupFacade;
    }
}
