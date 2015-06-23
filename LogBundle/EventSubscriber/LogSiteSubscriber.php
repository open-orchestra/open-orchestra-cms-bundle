<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\SiteEvent;
use OpenOrchestra\ModelInterface\Model\SiteInterface;
use OpenOrchestra\ModelInterface\SiteEvents;

/**
 * Class LogSiteSubscriber
 */
class LogSiteSubscriber extends AbstractLogSubscriber
{
    /**
     * @param SiteEvent $event
     */
    public function siteCreate(SiteEvent $event)
    {
        $this->sendLog('open_orchestra_log.site.create', $event->getSite());
    }

    /**
     * @param SiteEvent $event
     */
    public function siteDelete(SiteEvent $event)
    {
        $this->sendLog('open_orchestra_log.site.delete', $event->getSite());
    }

    /**
     * @param SiteEvent $event
     */
    public function siteUpdate(SiteEvent $event)
    {
        $this->sendLog('open_orchestra_log.site.update', $event->getSite());
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

    /**
     * @param string        $message
     * @param SiteInterface $site
     */
    protected function sendLog($message, SiteInterface $site)
    {
        $this->logger->info($message, array(
            'site_id' => $site->getSiteId(),
            'site_name' => $site->getName()
        ));
    }
}
