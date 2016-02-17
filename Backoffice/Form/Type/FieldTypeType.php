<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventListener\TranslateValueInitializerListener;
use OpenOrchestra\Backoffice\EventSubscriber\FieldTypeTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use OpenOrchestra\ModelBundle\Document\FieldType;

/**
 * Class FieldTypeType
 */
class FieldTypeType extends AbstractType
{
    protected $translateValueInitializer;
    protected $fieldOptionClass;
    protected $fieldTypeClass;
    protected $fieldOptions;
    protected $fieldTypeParameters;

    /**
     * @param TranslateValueInitializerListener $translateValueInitializer
     * @param array                             $fieldOptions
     * @param string                            $fieldOptionClass
     * @param string                            $fieldTypeClass
     * @param array                             $fieldTypeParameters
     */
    public function __construct(
        TranslateValueInitializerListener $translateValueInitializer,
        array $fieldOptions,
        $fieldOptionClass,
        $fieldTypeClass,
        array $fieldTypeParameters
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->fieldOptions = $fieldOptions;
        $this->fieldOptionClass = $fieldOptionClass;
        $this->fieldTypeClass = $fieldTypeClass;
        $this->fieldTypeParameters = $fieldTypeParameters;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, array($this->translateValueInitializer, 'preSetData'));
        $builder->addEventListener(FormEvents::PRE_SUBMIT, array($this->translateValueInitializer, 'preSubmitFieldType'));
        if (array_key_exists('property_path', $options) && is_null($options['property_path'])){
            $builder->setData($options['prototype_data']());
        }
        $builder
            ->add('fieldId', 'text', array(
                'label' => 'open_orchestra_backoffice.form.field_type.field_id'
            ))
            ->add('labels', 'oo_translated_value_collection', array(
                'label' => 'open_orchestra_backoffice.form.field_type.labels'
            ))
            ->add('searchable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.searchable',
                'required' => false,
            ))
            ->add('translatable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.translatable',
                'required' => false,
            ))
            ->add('type', 'choice', array(
                'choices' => $this->getChoicesType(),
                'label' => 'open_orchestra_backoffice.form.field_type.type',
                'attr' => array(
                    'class' => 'content_type_change_type'
                )
            ))
            ->add('listable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.listable',
                'required' => false,
            ));
        $builder->addEventSubscriber(new FieldTypeTypeSubscriber($this->fieldOptions, $this->fieldOptionClass, $this->fieldTypeParameters));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'oo_field_type';
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->fieldTypeClass,
            'label' => 'open_orchestra_backoffice.form.field_type.label',
            'prototype_data' => function(){
                $default = each($this->fieldOptions);
                $fieldType = new FieldType();
                $fieldType->setType($default['key']);

                return $fieldType;
            }
        ));
    }

    /**
     * @return array
     */
    protected function getChoicesType()
    {
        $choices = array();
        foreach ($this->fieldOptions as $key => $option) {
            $choices[$key] = $option['label'];
        }
        asort($choices);

        return $choices;
    }
}
