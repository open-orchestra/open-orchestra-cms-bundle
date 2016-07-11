<?php

namespace OpenOrchestra\Backoffice\EventSubscriber;

use OpenOrchestra\Backoffice\Form\Type\Component\ColumnLayoutRowType;
use OpenOrchestra\Backoffice\Manager\AreaManager;
use OpenOrchestra\ModelInterface\Model\AreaInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class AreaRowSubscriber
 */
class AreaRowSubscriber implements EventSubscriberInterface
{
    protected $areaManager;

    /**
     * @param AreaManager $areaManager
     */
    public function __construct(AreaManager $areaManager)
    {
        $this->areaManager = $areaManager;
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $area = $event->getData();
        $form = $event->getForm();
        $columnLayout = '';
        if (null !== $area && null !== $area->getAreaId()) {
            $listColumnWidth = array();
            foreach ($area->getAreas() as $area) {
                $listColumnWidth[] = $area->getWidth();
            }
            $columnLayout = implode(',', $listColumnWidth);
        }

        $form->add('columnLayout', ColumnLayoutRowType::class, array(
            'label' => 'open_orchestra_backoffice.form.area.column_layout.label',
            'mapped' => false,
            'attr' => array(
                'help_text' => 'open_orchestra_backoffice.form.area.column_layout.helper',
            ),
            'data' => array('layout' => $columnLayout)
        ));
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        /** @var AreaInterface $area */
        $form = $event->getForm();
        $area = $form->getData();
        if (array_key_exists('columnLayout', $data) && array_key_exists('layout', $data['columnLayout'])) {
            $columnsLayout = explode(',', $data['columnLayout']['layout']);
            $columnAreas = $area->getAreas();
            $countColumnArea = count($columnAreas);
            $countColumnsLayout = count($columnsLayout);
            if ($countColumnArea > $countColumnsLayout) {
                for($i = $countColumnsLayout; $i < $countColumnArea; $i++) {
                    $columnAreas->remove($i);
                }
            }

            foreach ($columnsLayout as $key => $columnWidth) {
                $columnWidth = trim($columnWidth);
                if (isset($columnAreas[$key])) {
                    $column = $columnAreas[$key];
                    $column->setWidth($columnWidth);
                } else {
                    /** @var AreaInterface $column */
                    $column = $this->areaManager->initializeNewAreaColumn($area);
                    $column->setLabel(str_replace('_', ' ', $column->getAreaId()));
                    $column->setWidth($columnWidth);
                    $columnAreas->add($column);
                }
            }

            $area->setAreas($columnAreas);
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
}
