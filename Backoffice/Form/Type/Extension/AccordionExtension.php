<?php

namespace OpenOrchestra\Backoffice\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class AccordeonExtension
 */
class AccordionExtension extends AbstractTypeExtension
{
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if ($form->getParent() &&
            $form->getParent()->getConfig()->getType()->getName() == 'collection' &&
            $form->getConfig()->getOption('columns')
        ) {
            $columns = $form->getConfig()->getOption('columns');

            foreach($columns as $column) {
                if ($form->has($column)) {
                    $label = $form->get($column)->getConfig()->getOption('label');
                    $data = $form->get($column)->getData();
                    $view->vars['columns'][$label] = $data;
                }
            }
        }
    }

    /**
     * Returns the name of extended type.
     *
     * @return string The name of extended type
     */
    public function getExtendedType()
    {
        return 'form';
    }
}
