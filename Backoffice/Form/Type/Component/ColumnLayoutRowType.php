<?php

namespace OpenOrchestra\Backoffice\Form\Type\Component;

use OpenOrchestra\Backoffice\Validator\Constraints\AreaRowLayout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ColumnLayoutRowType
 */
class ColumnLayoutRowType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('layout', TextType::class, array(
            'label' => 'open_orchestra_backoffice.form.area.layout.manually',
            'constraints' => array(
                new AreaRowLayout()
            ),
        ));
    }
}
