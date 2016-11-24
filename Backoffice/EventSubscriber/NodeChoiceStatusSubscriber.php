<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\BackofficeBundle\StrategyManager\AuthorizeStatusChangeManager;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeChoiceStatusSubscriber
 */
class NodeChoiceStatusSubscriber implements EventSubscriberInterface
{
    protected $authorizeStatusChangeManager;

    /**
     * @param AuthorizeStatusChangeManager $authorizeStatusChangeManager
     */
    public function __construct(AuthorizeStatusChangeManager $authorizeStatusChangeManager)
    {
        $this->authorizeStatusChangeManager = $authorizeStatusChangeManager;
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
        $choices = array();
        $choices[] = $node->getStatus();
        $transitions = $node->getStatus()->getFromRoles();

        foreach ($transitions as $transition) {
            $status = $transition->getToStatus();
            if ($this->authorizeStatusChangeManager->isGranted($node, $status)) {
                $choices[] = $status;
            }
        }

        return $choices;
    }
}
