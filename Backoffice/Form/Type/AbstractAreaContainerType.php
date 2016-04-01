<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class AbstractAreaContainerType
 */
abstract class AbstractAreaContainerType extends AbstractType
{

    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildAreaListView(FormView $view, FormInterface $form, array $options)
    {
        $areaContainer = $view->vars['value'];
        $areas = $areaContainer->getAreas();
        $view->vars['areas'] = array();
        foreach($areas as $area) {
            $view->vars['areas'][] = $area->getAreaId();
        }
    }

}
