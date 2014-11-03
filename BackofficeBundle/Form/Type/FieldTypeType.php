<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use PHPOrchestra\BaseBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class FieldTypeType
 */
class FieldTypeType extends AbstractType
{
    protected $translateValueInitializer;
    protected $translator;

    /**
     * @param TranslatorInterface               $translator
     * @param TranslateValueInitializerListener $translateValueInitializer
     */
    public function __construct(
        TranslatorInterface $translator,
        TranslateValueInitializerListener $translateValueInitializer
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));
        $builder
            ->add('fieldId', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.field_id'
            ))
            ->add('labels', 'translated_value_collection', array(
                'label' => 'php_orchestra_backoffice.form.field_type.field_labels'
            ))
            ->add('defaultValue', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.default_value'
            ))
            ->add('searchable', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.searchable'
            ))
            ->add('type', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.type'
            ));
        $builder->add('options', 'collection', array(
            'type' => 'field_option',
            'allow_add' => true,
            'allow_delete' => false,
            'label' => 'php_orchestra_backoffice.form.field_type.options',
            'attr' => array(
                'data-prototype-label-add' => $this->translator->trans('php_orchestra_backoffice.form.field_option.add'),
                'data-prototype-label-new' => $this->translator->trans('php_orchestra_backoffice.form.field_option.new'),
                'data-prototype-label-remove' => $this->translator->trans('php_orchestra_backoffice.form.field_option.delete'),
            )
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'field_type';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'PHPOrchestra\ModelBundle\Document\FieldType',
            'label' => $this->translator->trans('php_orchestra_backoffice.form.field_type.label')
        ));
    }
}
