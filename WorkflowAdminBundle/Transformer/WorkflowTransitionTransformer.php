<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Exceptions\TransformerParameterTypeException;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\ModelInterface\Model\WorkflowTransitionInterface;

/**
 * Class WorkflowTransitionTransformer
 */
class WorkflowTransitionTransformer extends AbstractTransformer
{
    /**
     * @param WorkflowTransitionInterface $workflowTransition
     *
     * @return FacadeInterface
     *
     * @throws TransformerParameterTypeException
     */
    public function transform($workflowTransition)
    {
        if (!$workflowTransition instanceof WorkflowTransitionInterface) {
            throw new TransformerParameterTypeException();
        }

        $facade = $this->newFacade();
        $facade->statusFrom = $this->getTransformer('status')->transform($workflowTransition->getStatusFrom());
        $facade->statusTo = $this->getTransformer('status')->transform($workflowTransition->getStatusTo());

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_transition';
    }
}
