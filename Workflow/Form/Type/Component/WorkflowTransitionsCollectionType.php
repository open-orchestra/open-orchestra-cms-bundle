<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowTransitionsCollectionType
 */
class WorkflowTransitionsCollectionType extends AbstractType
{
    protected $transitionsTransformer;

    public function __construct($transitionTransformer)
    {
        $this->transitionsTransformer = $transitionTransformer;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder->addModelTransformer($this->transitionsTransformer);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
                'type' => 'checkbox',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_transitions_collection';
    }
}
