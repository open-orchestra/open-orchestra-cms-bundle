<?php

namespace PHPOrchestra\BackofficeBundle\Form\Type;

use PHPOrchestra\BackofficeBundle\EventListener\TranslateValueInitializerListener;
use PHPOrchestra\BackofficeBundle\EventSubscriber\FieldTypeTypeSubscriber;
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
    protected $fieldOptions;
    protected $translator;

    /**
     * @param TranslatorInterface               $translator
     * @param TranslateValueInitializerListener $translateValueInitializer
     * @param array                             $fieldOptions
     */
    public function __construct(
        TranslatorInterface $translator,
        TranslateValueInitializerListener $translateValueInitializer,
        array $fieldOptions
    )
    {
        $this->translateValueInitializer = $translateValueInitializer;
        $this->translator = $translator;
        $this->fieldOptions = $fieldOptions;
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
                'label' => 'php_orchestra_backoffice.form.field_type.labels'
            ))
            ->add('defaultValue', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.default_value'
            ))
            ->add('searchable', 'text', array(
                'label' => 'php_orchestra_backoffice.form.field_type.searchable'
            ))
            ->add('type', 'choice', array(
                'choices' => $this->getChoices(),
                'label' => 'php_orchestra_backoffice.form.field_type.type'
            ));
        $builder->addEventSubscriber(new FieldTypeTypeSubscriber($this->fieldOptions));
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
