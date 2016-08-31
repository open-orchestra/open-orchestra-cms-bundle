<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use OpenOrchestra\Backoffice\EventSubscriber\FieldTypeTypeSubscriber;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class FieldTypeType
 */
class FieldTypeType extends AbstractType
{
    protected $fieldOptionClass;
    protected $fieldTypeClass;
    protected $fieldOptions;
    protected $fieldTypeParameters;
    protected $backOfficeLanguages;

    /**
     * @param array  $fieldOptions
     * @param string $fieldOptionClass
     * @param string $fieldTypeClass
     * @param array  $fieldTypeParameters
     * @param array  $backOfficeLanguages
     */
    public function __construct(
        array $fieldOptions,
        $fieldOptionClass,
        $fieldTypeClass,
        array $fieldTypeParameters,
        array  $backOfficeLanguages
    )
    {
        $this->fieldOptions = $fieldOptions;
        $this->fieldOptionClass = $fieldOptionClass;
        $this->fieldTypeClass = $fieldTypeClass;
        $this->fieldTypeParameters = $fieldTypeParameters;
        $this->backOfficeLanguages = $backOfficeLanguages;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (array_key_exists('property_path', $options) && is_null($options['property_path'])){
            $builder->setData($options['prototype_data']());
        }
        $builder
            ->add('fieldId', 'text', array(
                'label' => 'open_orchestra_backoffice.form.field_type.field_id',
                'attr' => array(
                    'help_text' => 'open_orchestra_backoffice.form.allowed_characters.helper',
                )
            ))
            ->add('labels', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.field_type.labels',
                'languages' => $this->backOfficeLanguages
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
            ))
            ->add('translatable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.translatable',
                'required' => false,
            ))
            ->add('searchable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.searchable',
                'required' => false,
            ))
            ->add('orderable', 'checkbox', array(
                'label' => 'open_orchestra_backoffice.form.field_type.orderable',
                'required' => false,
            ))
            ->add('orderDirection', 'choice', array(
                'choices' => $this->getChoicesOrder(),
                'label' => 'open_orchestra_backoffice.form.field_type.orderDirection',
                'required' => false,
            ))
            ->add('position', 'integer', array(
                'label' => 'open_orchestra_backoffice.form.field_type.position',
                'required' => false,
                'attr' => array(
                    'class' => 'oo-field-position'
                )
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
                $fieldType = new $this->fieldTypeClass();
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

    /**
     * @return array
     */
    protected function getChoicesOrder()
    {
       return array(
           "asc" => 'open_orchestra_backoffice.form.field_type.order_direction_choice.asc',
           "desc" => 'open_orchestra_backoffice.form.field_type.order_direction_choice.desc'
       );
    }
}
