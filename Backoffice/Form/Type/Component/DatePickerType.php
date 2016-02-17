<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class DatePickerType
 */
class DatePickerType extends DateType
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        parent::finishView($view, $form, $options);
        $view->vars['format'] = $options['format'];
    }

    /**
     * Returns the name of the bloc prefix.
     *
     * @return string The name of the bloc prefix
     */
    public function getBlockPrefix()
    {
        return 'oo_date_picker';
    }
}
