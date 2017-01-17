<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowStatusParametersType
 */
class WorkflowStatusParametersType extends AbstractType
{
    protected $statusClass;

    /**
     * @param string $statusClass
     */
    public function __construct($statusClass)
    {
        $this->statusClass = $statusClass;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('initialState'        , 'checkbox', array('required' => false))
            ->add('translationState'    , 'checkbox', array('required' => false))
            ->add('publishedState'      , 'checkbox', array('required' => false))
            ->add('autoPublishFromState', 'checkbox', array('required' => false))
            ->add('autoUnpublishToState', 'checkbox', array('required' => false))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', $this->statusClass);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_status_parameters';
    }
}
