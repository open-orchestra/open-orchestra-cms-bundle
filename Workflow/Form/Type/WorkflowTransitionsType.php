<?php

namespace OpenOrchestra\Workflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class WorkflowTransitionsType
 */
class WorkflowTransitionsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('profiles', 'collection', array(
            'type'         => 'oo_workflow_profile_transitions',
            'allow_add'    => false,
            'allow_delete' => false,
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_transitions';
    }
}