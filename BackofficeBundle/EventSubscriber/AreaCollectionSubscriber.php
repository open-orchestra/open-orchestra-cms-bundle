<?php

namespace OpenOrchestra\BackofficeBundle\EventSubscriber;

use OpenOrchestra\ModelInterface\Model\AreaInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Class AreaCollectionSubscriber
 */
class AreaCollectionSubscriber implements EventSubscriberInterface
{
    protected $areaClass;

    /**
     * @param string $areaClass
     */
    public function __construct($areaClass)
    {
        $this->areaClass = $areaClass;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $areaContainer = $form->getData();
        $data = $event->getData();

        if (array_key_exists('newAreas', $data)) {
            foreach ($data['newAreas'] as $newAreaData) {
                $areaClass = $this->areaClass;
                /** @var AreaInterface $newArea */
                $newArea = new $areaClass();
                $newArea->setAreaId($newAreaData);

                $areaContainer->addArea($newArea);
            }
        }
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $areaContainer = $event->getData();

        if (
            (!$areaContainer instanceof NodeInterface && 0 == count($areaContainer->getBlocks()))
            || ($areaContainer instanceof NodeInterface && $areaContainer->getId() )
        ) {
            $form->add('newAreas', 'collection', array(
                'type' => 'text',
                'allow_add' => true,
                'mapped' => false,
                'label' => 'open_orchestra_backoffice.form.area.new_areas',
                'attr' => array(
                    'data-prototype-label-add' => 'Ajout',
                    'data-prototype-label-new' => 'Nouveau',
                    'data-prototype-label-remove' => 'Suppression',
                )
            ));
        }
    }
}
