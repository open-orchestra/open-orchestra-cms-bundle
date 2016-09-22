<?php

namespace OpenOrchestra\Backoffice\Form\Type\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FiedlTypeTabulationExtension
 */
class FieldTypeTabulationExtension extends AbstractTypeExtension
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAttribute('tabulation_rank', $options['tabulation_rank']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['tabulation_rank'] = $form->getConfig()->getAttribute('tabulation_rank');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'tabulation_rank' => 0,
        ));
    }

    public function getExtendedType()
    {
        return 'form';
    }
}
