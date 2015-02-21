<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\ThemeEvents;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class LogThemeSubscriber
 */
class LogThemeSubscriber implements EventSubscriberInterface
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
     * @param ThemeEvent $event
     */
    public function themeCreate(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $this->logger->info('open_orchestra_log.theme.create', array(
            'theme_name' => $theme->getName()
        ));
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeDelete(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $this->logger->info('open_orchestra_log.theme.delete', array(
            'theme_name' => $theme->getName()
        ));
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeUpdate(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $this->logger->info('open_orchestra_log.theme.update', array(
            'theme_name' => $theme->getName()
        ));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ThemeEvents::THEME_CREATE => 'themeCreate',
            ThemeEvents::THEME_DELETE => 'themeDelete',
            ThemeEvents::THEME_UPDATE => 'themeUpdate',
        );
    }
}
