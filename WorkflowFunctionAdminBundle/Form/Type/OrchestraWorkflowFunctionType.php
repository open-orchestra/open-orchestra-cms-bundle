<?php
namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraWorkflowFunctionType
 */
class OrchestraWorkflowFunctionType extends AbstractType
{
    /**
     * @var string
     */
    private $workflowFunctionClass;

    /**
     * @param $workflowFunctionClass
     */
    public function __construct($workflowFunctionClass)
    {
        $this->workflowFunctionClass = $workflowFunctionClass;
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'class' => $this->workflowFunctionClass,
                'property' => 'name'
            )
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'document';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'orchestra_workflow_function';
    }
}
