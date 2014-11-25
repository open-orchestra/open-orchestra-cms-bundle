<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FieldOptionType
 */
class FieldOptionType extends AbstractType
{
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('key', 'hidden', array('label' => 'php_orchestra_backoffice.form.field_option.key'));
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $element = $event->getData();
            $form = $event->getForm();
            $form->add('value', 'text', array(
                'label' => $element->getKey(),
                'attr' => array('class' => 'field_type_option'),
            ));
        });
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
            'data_class' => 'PHPOrchestra\ModelBundle\Document\FieldOption',
            'label' => $this->translator->trans('php_orchestra_backoffice.form.field_option.label'),
        ));
    }
}
