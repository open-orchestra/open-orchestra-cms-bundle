<?php

namespace OpenOrchestra\Backoffice\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Event\EventTrait\EventStatusableInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\ModelInterface\Repository\StatusableRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class UpdateStatusableElementPublished
 */
class UpdateStatusableElementPublished
{
    protected $repository;
    protected $objectManager;
    protected $statusRepository;

    /**
     * @param StatusableRepositoryInterface $repository
     * @param StatusRepositoryInterface     $statusRepository
     * @param ObjectManager                 $objectManager
     */
    public function __construct(
        StatusableRepositoryInterface $repository,
        StatusRepositoryInterface $statusRepository,
        ObjectManager $objectManager
    ) {
        $this->repository = $repository;
        $this->statusRepository = $statusRepository;
        $this->objectManager = $objectManager;
    }

    /**
     * @param EventStatusableInterface $event
     */
    public function updateStatus(EventStatusableInterface $event)
    {
        $statusableElement = $event->getStatusableElement();

        if ($statusableElement->getStatus()->isPublishedState()) {
            $elementsPublished = $this->repository->findPublished($statusableElement);
            if (!empty($elementsPublished)) {

                $statusUnPublish = $this->statusRepository->findOneByAutoUnpublishTo();
                /** @var StatusableInterface $elementPublished */
                foreach ($elementsPublished as $elementPublished) {
                    $elementPublished->setStatus($statusUnPublish);
                }

                $this->objectManager->flush();
            }
        }
    }
}
