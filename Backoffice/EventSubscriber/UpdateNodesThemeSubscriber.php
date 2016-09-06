<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Event\ThemeEvent;
use OpenOrchestra\ModelInterface\Repository\NodeRepositoryInterface;
use OpenOrchestra\ModelInterface\ThemeEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateNodesThemeSubscriber
 */
class UpdateNodesThemeSubscriber implements EventSubscriberInterface
{
    protected $nodeRepository;
    protected $objectManager;

    /**
     * @param NodeRepositoryInterface $nodeRepository
     * @param ObjectManager           $objectManager
     */
    public function __construct(NodeRepositoryInterface $nodeRepository, ObjectManager $objectManager)
    {
        $this->nodeRepository = $nodeRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param ThemeEvent $event
     */
    public function updateTheme(ThemeEvent $event)
    {
        $theme = $event->getTheme();
        $oldTheme = $event->getOldTheme();
        if ($theme->getName() !== $oldTheme->getName()) {
            $nodesToUpdate = $this->nodeRepository->findByTheme($oldTheme->getName());
            foreach ($nodesToUpdate as $node) {
                $node->setTheme($theme->getName());
            }

            $this->objectManager->flush();
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            ThemeEvents::THEME_UPDATE => 'updateTheme',
        );
    }
}
