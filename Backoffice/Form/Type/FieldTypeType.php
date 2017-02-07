<?php

namespace OpenOrchestra\Backoffice\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;

/**
 * Class FieldTypeType
 */
class FieldTypeType extends AbstractType
{
    protected $contextManager;
    protected $fieldTypeTypeSubscriber;
    protected $backOfficeLanguages;
    protected $fieldTypeParameters;
    protected $fieldTypeClass;

    /**
     * @param CurrentSiteIdInterface   $contextManager
     * @param EventSubscriberInterface $fieldTypeTypeSubscriber
     * @param array                    $backOfficeLanguages
     * @param array                    $fieldTypeParameters
     * @param string                   $fieldTypeClass
     */
    public function __construct(
        CurrentSiteIdInterface $contextManager,
        EventSubscriberInterface $fieldTypeTypeSubscriber,
        array $backOfficeLanguages,
        array $fieldTypeParameters,
        $fieldTypeClass
    )
    {
        $this->contextManager = $contextManager;
        $this->fieldTypeTypeSubscriber = $fieldTypeTypeSubscriber;
        $this->backOfficeLanguages = $backOfficeLanguages;
        $this->fieldTypeParameters = $fieldTypeParameters;
        $this->fieldTypeClass = $fieldTypeClass;
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
            ->add('labels', 'oo_multi_languages', array(
                'label' => 'open_orchestra_backoffice.form.field_type.labels',
                'languages' => $this->backOfficeLanguages,
                'sub_group_id' => 'property',
            ))
            ->add('fieldId', 'text', array(
                'label' => 'open_orchestra_backoffice.form.field_type.field_id',
                'attr' => array(
                    'help_text' => 'open_orchestra_backoffice.form.allowed_characters.helper',
                ),
                'sub_group_id' => 'property',
            ))
            ->add('type', 'choice', array(
                'choices' => $this->getChoicesType(),
                'label' => 'open_orchestra_backoffice.form.field_type.type',
                'attr' => array(
                    'class' => 'patch-submit-change'
                ),
                'sub_group_id' => 'property',
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
            ->add('options', 'oo_field_option', array(
                'sub_group_id' => 'parameter',
                'attr' => array('class' => 'subform-to-refresh'),
                'label' => false,
            ))
            ->add('container', 'form', array(
                'inherit_data' => true,
                'label' => false,
                'sub_group_id' => 'parameter',
                'attr' => array('class' => 'subform-to-refresh'),
            ));
        $builder->addEventSubscriber($this->fieldTypeTypeSubscriber);
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
            'attr' => array('class' => 'form-to-patch'),
            'group_enabled' => true,
            'sub_group_render' => array(
                'property' => array(
                    'rank' => 0,
                    'label' => 'open_orchestra_backoffice.form.field_type.sub_group.property',
                ),
                'parameter' => array(
                    'rank' => 1,
                    'label' => 'open_orchestra_backoffice.form.field_type.sub_group.parameter',
                ),
            ),
            'columns' => array('labels', 'fieldId', 'type', 'options'),
            'label' => 'open_orchestra_backoffice.form.field_type.label',
            'prototype_data' => function(){
                $default = each($this->fieldTypeParameters);
                $fieldType = new $this->fieldTypeClass();
                $fieldType->setType($default['key']);

                return $fieldType;
            }
        ));
    }


    /**
     * @param FormView      $view
     * @param FormInterface $form
     * @param array         $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $data = $view->vars['columns']['labels']['data'];

        $language = $this->contextManager->getCurrentLocale();
        $view->vars['columns']['labels']['data'] = (is_array($data) && array_key_exists($language, $data)) ? $data[$language] : '';

        $data = $view->vars['columns']['type']['data'];
        $types = $this->getChoicesType();
        $view->vars['columns']['type']['data'] = (array_key_exists($data, $types)) ? $types[$data] : '';

        $value = 'open_orchestra_backoffice.form.swchoff.off';

        if (array_key_exists('options', $view->vars['columns'])) {
            foreach ($view->vars['columns']['options']['data'] as $data) {
                if ($data->getKey() == 'required') {
                    $value = $data->getValue() ? 'open_orchestra_backoffice.form.swchoff.on' : 'open_orchestra_backoffice.form.swchoff.off';
               }
            }
        }
        $view->vars['columns']['options'] = array(
            'label' => 'open_orchestra_backoffice.form.orchestra_fields.required_field',
            'data' => $value,
        );
    }

    /**
     * @return array
     */
    protected function getChoicesType()
    {
        $choices = array();
        foreach ($this->fieldTypeParameters as $key => $option) {
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
