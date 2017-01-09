<?php

namespace OpenOrchestra\Workflow\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use OpenOrchestra\Workflow\Validator\Constraints\WorkflowParameter;

/**
 * Class WorkflowParametersType
 */
class WorkflowParametersType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('statuses', 'collection', array(
                'type'         => 'oo_workflow_status_parameters',
                'allow_add'    => false,
                'allow_delete' => false,
                'constraints'  => array(new WorkflowParameter())
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'oo_workflow_parameters';
    }
}
