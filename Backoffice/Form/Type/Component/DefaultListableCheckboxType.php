<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Class DefaultListableCheckboxType
 */
class DefaultListableCheckboxType extends AbstractType
{
    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if (isset($view->vars['name']) && '' !== $view->vars['name'] && null !== $view->vars['name']) {
            $view->vars['label'] = 'open_orchestra_backoffice.form.content_type.default_listable_label.'. $view->vars['name'];
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_default_listable_checkbox';
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return CheckboxType::class;
    }
}
