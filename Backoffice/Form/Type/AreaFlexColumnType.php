<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AreaFlexColumnType
 */
class AreaFlexColumnType extends AbstractAreaFlexType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('label', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.label'
        ));
        $builder->add('width', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.width'
        ));
        $builder->add('htmlClass', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area_flex.html_class',
            'required' => false
        ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault(
            'attr',
            array('data-title' => $this->translator->trans('open_orchestra_backoffice.form.area_flex.edit_column_title'))
        );
    }
}
