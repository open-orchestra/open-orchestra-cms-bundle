<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class AreaColumnType
 */
class AreaColumnType extends AbstractAreaType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('label', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area.label'
        ));
        $builder->add('width', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area.width'
        ));
        $builder->add('htmlClass', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area.html_class',
            'required' => false
        ));
    }
}
