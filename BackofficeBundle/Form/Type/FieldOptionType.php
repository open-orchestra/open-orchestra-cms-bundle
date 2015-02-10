<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventSubscriber\FieldOptionTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FieldOptionType
 */
class FieldOptionType extends AbstractType
{
    protected $fieldOptionClass;
    protected $translator;
    protected $options;

    /**
     * @param TranslatorInterface $translator
     * @param array               $options
     * @param string              $fieldOptionClass
     */
    public function __construct(TranslatorInterface $translator, array $options, $fieldOptionClass)
    {
        $this->fieldOptionClass = $fieldOptionClass;
        $this->translator = $translator;
        $this->options = $options;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'hidden', array('label' => 'php_orchestra_backoffice.form.field_option.key'));
        $builder->addEventSubscriber(new FieldOptionTypeSubscriber($this->options));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'field_option';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->fieldOptionClass,
            'label' => $this->translator->trans('php_orchestra_backoffice.form.field_option.label'),
        ));
    }
}
