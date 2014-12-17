<?php

namespace PHPOrchestra\BackofficeBundle\EventSubscriber;

use PHPOrchestra\ModelBundle\Document\Area;
use PHPOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


/**
 * Class AreaCollectionSubscriber
 */
class AreaCollectionSubscriber implements EventSubscriberInterface
{
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
                // TODO use a parameter
                $newArea = new Area();
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
                'label' => 'php_orchestra_backoffice.form.area.new_areas',
                'attr' => array(
                    'data-prototype-label-add' => 'Ajout',
                    'data-prototype-label-new' => 'Nouveau',
                    'data-prototype-label-remove' => 'Suppression',
                )
            ));
        }
    }
}
