<?php

namespace PHPOrchestra\LogBundle\EventSubscriber;

use PHPOrchestra\ModelInterface\Event\ThemeEvent;
use PHPOrchestra\ModelInterface\ThemeEvents;
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
        $this->logger->info('Create a new theme', array($theme->getName()));
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeDelete(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $this->logger->info('Delete a theme', array($theme->getName()));
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeUpdate(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $this->logger->info('Update a theme', array($theme->getName()));
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            ThemeEvents::THEME_CREATE => 'themeEvent',
            ThemeEvents::THEME_DELETE => 'themeEvent',
            ThemeEvents::THEME_UPDATE => 'themeEvent',
        );
    }
}
