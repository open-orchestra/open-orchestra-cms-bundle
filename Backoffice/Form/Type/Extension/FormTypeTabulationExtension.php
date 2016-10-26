<?php

namespace OpenOrchestra\Backoffice\Form\Type\Extension;;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * Class FormTypeTabulationExtension
 */
class FormTypeTabulationExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('tabulation_enabled', $options['tabulation_enabled']);
        $builder->setAttribute('tabulation_label', $options['tabulation_label']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['tabulation_enabled'] = $form->getConfig()->getAttribute('tabulation_enabled');
        $view->vars['tabulation_label'] = $form->getConfig()->getAttribute('tabulation_label');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'tabulation_enabled' => false,
            'tabulation_label' => array(),
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
