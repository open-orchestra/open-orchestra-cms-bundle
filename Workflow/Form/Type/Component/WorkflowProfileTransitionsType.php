<?php

namespace OpenOrchestra\Workflow\Form\Type\Component;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class WorkflowProfileTransitionsType
 */
class WorkflowProfileTransitionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
         $builder
            ->add('transitions', 'oo_workflow_transitions_collection', array(
         ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', 'OpenOrchestra\ModelBundle\Document\WorkflowProfile');
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_profile_transitions';
    }
}
