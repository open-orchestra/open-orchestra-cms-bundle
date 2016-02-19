<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

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
}
