<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\FieldOptionTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FieldOptionType
 */
class FieldOptionType extends AbstractType
{
    protected $fieldOptionClass;
    protected $translator;
    protected $options;

    /**
     * @param array  $options
     * @param string $fieldOptionClass
     */
    public function __construct(array $options, $fieldOptionClass)
    {
        $this->fieldOptionClass = $fieldOptionClass;
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'hidden', array('label' => 'open_orchestra_backoffice.form.field_option.key'));
        $builder->addEventSubscriber(new FieldOptionTypeSubscriber($this->options));
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
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->fieldOptionClass,
            'label' => 'open_orchestra_backoffice.form.field_option.label',
        ));
    }
}
