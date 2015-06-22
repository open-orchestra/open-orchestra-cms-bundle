<?php

namespace OpenOrchestra\LogBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\Model\ThemeInterface;
use OpenOrchestra\ModelInterface\ThemeEvents;

/**
 * Class LogThemeSubscriber
 */
class LogThemeSubscriber extends AbstractLogSubscriber
{
    /**
     * @param ThemeEvent $event
     */
    public function themeCreate(ThemeEvent $event)
    {
        $this->sendLog('open_orchestra_log.theme.create', $event->getTheme());
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeDelete(ThemeEvent $event)
    {
        $this->sendLog('open_orchestra_log.theme.delete', $event->getTheme());
    }

    /**
     * @param ThemeEvent $event
     */
    public function themeUpdate(ThemeEvent $event)
    {
        $this->sendLog('open_orchestra_log.theme.update', $event->getTheme());
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

    /**
     * @param string         $message
     * @param ThemeInterface $theme
     */
    protected function sendLog($message, ThemeInterface $theme)
    {
        $this->logger->info($message, array(
            'theme_name' => $theme->getName()
        ));
    }
}
