<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowFunctionAdminBundle\Facade\WorkflowFunctionCollectionFacade;

/**
 * Class WorkflowFunctionCollectionTransformer
 */
class WorkflowFunctionCollectionTransformer extends AbstractTransformer
{
    /**
     * @param ArrayCollection $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new WorkflowFunctionCollectionFacade();

        foreach ($mixed as $workflowFunction) {
            $facade->addWorkflowFunction($this->getTransformer('workflow_function')->transform($workflowFunction));
        }

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_workflow_function_new',
            array()
        ));

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
