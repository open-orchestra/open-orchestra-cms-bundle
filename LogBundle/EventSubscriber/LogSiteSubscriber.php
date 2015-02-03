<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\SiteEvent;
use PHPOrchestra\ModelInterface\SiteEvents;
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
        $this->logger->info('Create a new site', array('site_domain' => $site->getDomain()));
    }

    /**
     * @param SiteEvent $event
     */
    public function siteDelete(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->logger->info('Delete site', array('site_domain' => $site->getDomain()));
    }

    /**
     * @param SiteEvent $event
     */
    public function siteUpdate(SiteEvent $event)
    {
        $site = $event->getSite();
        $this->logger->info('Update site', array('site_domain' => $site->getDomain()));
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
