<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Event\StatusEvent;
use OpenOrchestra\ModelInterface\Repository\StatusableContainerRepositoryInterface;
use OpenOrchestra\ModelInterface\StatusEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UpdateEmbeddedStatusSubscriber
 */
class UpdateEmbeddedStatusSubscriber implements EventSubscriberInterface
{
    protected $statusableRepositories;

    /**
     * @param array $statusableRepositories
     */
    public function __construct(array $statusableRepositories)
    {
        foreach ($statusableRepositories as $statusableRepository) {
            if (! $statusableRepository instanceof StatusableContainerRepositoryInterface) {
                throw new \InvalidArgumentException('Repository should be an instance of StatusableContainerRepositoryInterface');
            }
        }
        $this->statusableRepositories = $statusableRepositories;
    }

    /**
     * @param StatusEvent $event
     */
    public function updateEmbeddedStatus(StatusEvent $event)
    {
        $status = $event->getStatus();
        /** @var StatusableContainerRepositoryInterface $statusableRepository */
        foreach ($this->statusableRepositories as $statusableRepository) {
            $statusableRepository->updateEmbeddedStatus($status);
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            StatusEvents::STATUS_UPDATE => 'updateEmbeddedStatus',
        );
    }

}
