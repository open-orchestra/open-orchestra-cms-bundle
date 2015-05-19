<?php
namespace OpenOrchestra\BackofficeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

/**
 * Class AbstractOrchestraWorkflowFunctionType
 */
abstract class AbstractOrchestraWorkflowFunctionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orchestra_workflow_function';
    }
}

