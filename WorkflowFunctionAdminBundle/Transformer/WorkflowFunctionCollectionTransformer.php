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
            $facade->addWorkflowFunction($this->getTransformer('workflowfunction')->transform($workflowFunction));
        }

        $facade->addLink('_self_add', $this->generateRoute(
            'open_orchestra_backoffice_workflowfunction_new',
            array()
        ));

        $facade->addLink('_translate', $this->generateRoute(
            'open_orchestra_api_translate'
        ));

        return $facade;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'workflowfunction_collection';
    }
}
