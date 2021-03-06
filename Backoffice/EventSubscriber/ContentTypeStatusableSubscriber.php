<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Repository\ContentRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Model\ContentTypeInterface;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;

/**
 * Class ContentTypeStatusableSubscriber
 */
class ContentTypeStatusableSubscriber implements EventSubscriberInterface
{
    protected $contentRepository;
    protected $statusRepository;

    /**
     * @param ContentRepositoryInterface $contentRepository
     * @param StatusRepositoryInterface  $statusRepository
     */
    public function __construct(
        ContentRepositoryInterface $contentRepository,
        StatusRepositoryInterface $statusRepository
    ) {
        $this->contentRepository = $contentRepository;
        $this->statusRepository = $statusRepository;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $contentType = $event->getForm()->getData();
        $data = $event->getData();

        $data['definingStatusable'] = !array_key_exists('definingStatusable', $data) ? false : $data['definingStatusable'];

        if ($contentType instanceof ContentTypeInterface
            && $contentType->isDefiningStatusable() != $data['definingStatusable']
        ) {
            if ($data['definingStatusable']) {
                $status = $this->statusRepository->findOneByInitial();
            } else {
                $status = $this->statusRepository->findOneByOutOfWorkflow();
            }
            $this->contentRepository->updateStatusByContentType($status, $contentType->getContentTypeId());
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }
}
