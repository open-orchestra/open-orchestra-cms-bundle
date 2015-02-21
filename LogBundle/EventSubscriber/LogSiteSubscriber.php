<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\SiteEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogSiteSubscriber
 */
class LogSiteSubscriber implements EventSubscriberInterface
{
    protected $logger;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param SiteEvent $event
     */
    public function siteCreate(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->logger->info('open_orchestra_log.site.create', array(
            'site_id' => $site->getSiteId(),
            'site_name' => $site->getName()
        ));
    }

    /**
     * @param SiteEvent $event
     */
    public function siteDelete(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->logger->info('open_orchestra_log.site.delete', array(
            'site_id' => $site->getSiteId(),
            'site_name' => $site->getName()
        ));
    }

    /**
     * @param SiteEvent $event
     */
    public function siteUpdate(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->logger->info('open_orchestra_log.site.update', array(
            'site_id' => $site->getSiteId(),
            'site_name' => $site->getName()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            SiteEvents::SITE_CREATE => 'siteCreate',
            SiteEvents::SITE_DELETE => 'siteDelete',
            SiteEvents::SITE_UPDATE => 'siteUpdate',
        );
    }
}
