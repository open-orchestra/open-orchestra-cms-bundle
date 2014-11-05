<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\BackofficeBundle\Manager\NodeManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class NodeChoiceSubscriber
 */
class NodeChoiceSubscriber implements EventSubscriberInterface
{
    protected $nodeManager;

    /**
     * @param NodeManager $nodeManager
     */
    public function __construct(NodeManager $nodeManager)
    {
        $this->nodeManager = $nodeManager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $node = $form->getData();
        $data = $event->getData();

        if (array_key_exists('nodeSource', $data) && is_null($node->getId())) {
            $this->nodeManager->hydrateNodeFromNodeId($node, $data['nodeSource']);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if (null === $data->getId()) {
            $form->add('nodeSource', 'orchestra_node_choice', array(
                'required' => false,
                'mapped' => false,
                'label' => 'php_orchestra_backoffice.form.node.node_source'
            ));
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => array('preSubmit', 100),
            FormEvents::PRE_SET_DATA => 'preSetData'
        );
    }
}
