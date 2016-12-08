<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use OpenOrchestra\ModelInterface\Repository\StatusRepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class NodeChoiceStatusSubscriber
 */
class NodeChoiceStatusSubscriber implements EventSubscriberInterface
{
    protected $statusRepository;
    protected $authorizationChecker;

    /**
     * @param StatusRepositoryInterface     $statusRepository
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
     public function __construct(
        StatusRepositoryInterface $statusRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->statusRepository = $statusRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null !== $data->getId()) {
            $form->add('status', 'oo_status_choice', array(
                'embedded' => true,
                'label' => 'open_orchestra_backoffice.form.node.status',
                'group_id' => 'properties',
                'sub_group_id' => 'publication',
                'choices' => $this->getStatusChoices($data)
            ));
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
     * @param NodeInterface $node
     *
     * @return array
     */
    protected function getStatusChoices(NodeInterface $node)
    {
        $choices = array($node->getStatus());
        $availableStatus = $this->statusRepository->findAll();

        foreach ($availableStatus as $status) {
            if ($status->getId() != $node->getStatus()->getId()
                && $this->authorizationChecker->isGranted($status, $node)
            ) {
                $choices[] = $status;
            }
        }

        return $choices;
    }
}
