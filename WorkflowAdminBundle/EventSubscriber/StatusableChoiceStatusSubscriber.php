<?php

namespace OpenOrchestra\WorkflowAdminBundle\EventSubscriber;

use Doctrine\Common\Persistence\ObjectManager;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class StatusableChoiceStatusSubscriber
 */
class StatusableChoiceStatusSubscriber implements EventSubscriberInterface
{
    protected $statusRepository;
    protected $authorizationChecker;
    protected $objectManager;
    protected $options;

    /**
     * @param StatusRepositoryInterface     $statusRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param ObjectManager                 $objectManager
     * @param array                         $options
     */
     public function __construct(
         StatusRepositoryInterface $statusRepository,
         AuthorizationCheckerInterface $authorizationChecker,
         ObjectManager $objectManager,
         array $options
    ) {
        $this->statusRepository = $statusRepository;
        $this->authorizationChecker = $authorizationChecker;
        $this->objectManager = $objectManager;
        $this->options = $options;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data->getId()) {
            $form->add('status', 'oo_status_choice', array_merge(array(
                'embedded' => true,
                'choices' => $this->getStatusChoices($data),
                ), $this->options)
            );
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }

    /**
     * @param StatusableInterface $statusable
     *
     * @return array
     */
    protected function getStatusChoices(StatusableInterface $statusable)
    {
        $currentStatus = $statusable->getStatus();
        $this->objectManager->persist($currentStatus);

        $choices = array($currentStatus);
        $availableStatus = $this->statusRepository->findAll();

        foreach ($availableStatus as $status) {
            if ($status->getId() != $statusable->getStatus()->getId()
                && $this->authorizationChecker->isGranted($status, $statusable)
            ) {
                $choices[] = $status;
            }
        }

        return $choices;
    }
}
