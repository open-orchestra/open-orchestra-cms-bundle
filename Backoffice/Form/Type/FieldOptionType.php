<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class FieldOptionType
 */
class FieldOptionType extends AbstractType
{
    protected $fieldOptionTransformer;

    /**
     * @param DataTransformerInterface $fieldTypeTypeTransformer
     */
    public function __construct(
        DataTransformerInterface $fieldOptionTransformer
    ) {
        $this->fieldOptionTransformer = $fieldOptionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->fieldOptionTransformer);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_field_option';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getParent()
    {
        return 'form';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'open_orchestra_backoffice.form.field_option.label',
        ));
    }
}
