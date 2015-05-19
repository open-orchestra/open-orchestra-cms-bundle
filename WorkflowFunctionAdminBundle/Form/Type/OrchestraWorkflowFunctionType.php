<?php
namespace OpenOrchestra\WorkflowFunctionAdminBundle\Form\Type;

use OpenOrchestra\BackofficeBundle\Form\Type\AbstractOrchestraWorkflowFunctionType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class OrchestraWorkflowFunctionType
 */
class OrchestraWorkflowFunctionType extends AbstractOrchestraWorkflowFunctionType
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
}
