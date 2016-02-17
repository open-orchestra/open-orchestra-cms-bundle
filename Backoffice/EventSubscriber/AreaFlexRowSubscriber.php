<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\BackofficeBundle\Manager\AreaFlexManager;
use OpenOrchestra\ModelInterface\Model\AreaFlexInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AreaFlexRowSubscriber
 */
class AreaFlexRowSubscriber implements EventSubscriberInterface
{
    protected $areaManager;

    /**
     * @param AreaFlexManager $areaManager
     */
    public function __construct(AreaFlexManager $areaManager)
    {
        $this->areaManager = $areaManager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        /** @var AreaFlexInterface $area */
        $form = $event->getForm();
        $area = $form->getData();
        if (array_key_exists('columnLayout', $data) && array_key_exists('layout', $data['columnLayout'])) {
            $columnsLayout = explode(',', $data['columnLayout']['layout']);
            foreach ($columnsLayout as $key => $columnWidth) {
                $columnWidth = trim($columnWidth);
                /** @var AreaFlexInterface $column */
                $column = $this->areaManager->initializeNewAreaColumn($area);
                $column->setLabel($column->getAreaId());
                $column->setWidth($columnWidth);
                $area->addArea($column);
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
        );
    }
}
