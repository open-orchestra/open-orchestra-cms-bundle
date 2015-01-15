<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use PHPOrchestra\BackofficeBundle\EventSubscriber\FieldTypeTypeSubscriber;
use PHPOrchestra\BackofficeBundle\EventSubscriber\AddSubmitButtonSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;
use PHPOrchestra\ModelBundle\Document\FieldType;
/**
 * Class FieldTypeType
 */
class FieldTypeType extends AbstractType
{
    protected $translateValueInitializer;
    protected $fieldOptionClass;
    protected $fieldTypeClass;
    protected $fieldOptions;
    protected $translator;

    /**
     * @param TranslatorInterface               $translator
     * @param TranslateValueInitializerListener $translateValueInitializer
     * @param array                             $fieldOptions
     * @param string                            $fieldOptionClass
     * @param string                            $fieldTypeClass
     */
    public function __construct(
        TranslatorInterface $translator,
        TranslateValueInitializerListener $translateValueInitializer,
        array $fieldOptions,
        $fieldOptionClass,
        $fieldTypeClass
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->translator = $translator;
        $this->fieldOptions = $fieldOptions;
        $this->fieldOptionClass = $fieldOptionClass;
        $this->fieldTypeClass = $fieldTypeClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));
        if(is_null($options['property_path'])){
            $builder->setData($options['prototype_data']());
        }
        $builder
            ->add('fieldId', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.field_id'
            ))
            ->add('labels', 'translated_value_collection', array(
                'label' => 'php_orchestra_backoffice.form.field_type.labels'
            ))
            ->add('defaultValue', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.default_value',
                'required' => false,
            ))
            ->add('searchable', 'checkbox', array(
                'label' => 'php_orchestra_backoffice.form.field_type.searchable',
                'required' => false,
            ))
            ->add('type', 'choice', array(
                'choices' => $this->getChoices(),
                'label' => 'php_orchestra_backoffice.form.field_type.type',
                'attr' => array(
                    'class' => 'content_type_change_type'
                )
            ));
        $builder->addEventSubscriber(new FieldTypeTypeSubscriber($this->fieldOptions, $this->fieldOptionClass));
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
            'data_class' => $this->fieldTypeClass,
            'label' => $this->translator->trans('php_orchestra_backoffice.form.field_type.label'),
            'prototype_data' => function(){
                $fieldType = new FieldType();
                $fieldType->setType('text');
                return $fieldType;
            }
        ));
    }

    /**
     * @return array
     */
    protected function getChoices()
    {
        $choices = array();
        foreach ($this->fieldOptions as $key => $option) {
            $choices[$key] = $this->translator->trans($option['label']);
        }

        return $choices;
    }
}
