<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
@trigger_error('The '.__NAMESPACE__.'\ChoiceArrayToStringTransformer class is deprecated since version 1.2.0 and will be removed in 2.0', E_USER_DEPRECATED);

/**
 * Class AbstractAreaContainerType
 * @deprecated will be removed in 2.0
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
        if ($form->has('newAreas')) {
            $errors = $form->get('newAreas')->getErrors();
            $erroredValues = array();
            foreach ($errors as $error) {
                $erroredValues = array_merge($erroredValues, $error->getOrigin()->getData());
            }
            $view->vars['areas'] = array();
            $areas = $areaContainer->getAreas();
            foreach ($areas as $area) {
                $view->vars['areas'][] = $area->getAreaId();
            }
            foreach ($erroredValues as $erroredValue) {
                if (false !== ($key = array_search($erroredValue, $view->vars['areas']))) {
                    unset($view->vars['areas'][$key]);
                }
            }
        }
    }
}
