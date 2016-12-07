<?php

namespace OpenOrchestra\WorkflowAdminBundle\Transformer;

use Doctrine\Common\Collections\Collection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractSecurityCheckerAwareTransformer;

/**
 * Class WorkflowFunctionCollectionTransformer
 */
class WorkflowFunctionCollectionTransformer extends AbstractSecurityCheckerAwareTransformer
{
    /**
     * @param Collection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = $this->newFacade();

        foreach ($mixed as $workflowFunction) {
            $facade->addWorkflowFunction($this->getTransformer('workflow_function')->transform($workflowFunction));
        }

        return $facade;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'workflow_function_collection';
    }
}
