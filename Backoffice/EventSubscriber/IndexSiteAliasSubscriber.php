<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\ModelInterface\SiteEvents;
use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\SiteInterface;

/**
 * Class IndexSiteAliasSubscriber
 */
class IndexSiteAliasSubscriber implements EventSubscriberInterface
{
    protected $objectManager;

    /**
     * @param ObjectManager           $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param SiteEvent $event
     */
    public function updateAliasId(SiteEvent $event)
    {
        $site = $event->getSite();
        $aliases = $site->getAliases();
        foreach($aliases as $key => $alias) {
            if (strpos($key, SiteInterface::PREFIX_SITE_ALIAS) === false) {
                $site->removeAlias($alias);
                $site->addAlias($alias);
            }
        }
        $this->objectManager->persist($site);
        $this->objectManager->flush($site);
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'updateAliasId',
            SiteEvents::SITE_UPDATE => 'updateAliasId',
        );
    }
}
