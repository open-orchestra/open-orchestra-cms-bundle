<?php

namespace OpenOrchestra\GroupBundle\EventSubscriber;

use OpenOrchestra\Backoffice\Repository\GroupRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ModelInterface\Event\SiteEvent;

/**
 * Class SoftDeleteGroupSubscriber
 */
class SoftDeleteGroupSubscriber implements EventSubscriberInterface
{
    protected $groupRepository;

    /**
     * @param GroupRepositoryInterface $groupRepository
     */
    public function __construct(GroupRepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * @param SiteEvent $event
     */
    public function softDeleteGroup(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->groupRepository->softDeleteGroupsBySite($site);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_DELETE => 'softDeleteGroup',
        );
    }
}
