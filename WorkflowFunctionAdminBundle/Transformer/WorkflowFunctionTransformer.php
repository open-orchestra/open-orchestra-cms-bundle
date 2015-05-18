<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Transformer;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use OpenOrchestra\BaseApi\Transformer\AbstractTransformer;
use OpenOrchestra\WorkflowFunctionAdminBundle\Facade\WorkflowFunctionFacade;
use OpenOrchestra\WorkflowFunction\Model\WorkflowFunctionInterface;

/**
 * Class WorkflowFunctionTransformer
 */
class WorkflowFunctionTransformer extends AbstractTransformer
{
    /**
     * @param WorkflowFunctionInterface $mixed
     *
     * @return FacadeInterface
     */
    public function transform($mixed)
    {
        $facade = new WorkflowFunctionFacade();

        $facade->id = $mixed->getId();
        $facade->name = $mixed->getName();

        $facade->addLink('_self', $this->generateRoute(
            'open_orchestra_api_workflowfunction_show',
            array('workflowFunctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_delete', $this->generateRoute(
            'open_orchestra_api_workflowfunction_delete',
            array('workflowFunctionId' => $mixed->getId())
        ));
        $facade->addLink('_self_form', $this->generateRoute(
            'open_orchestra_backoffice_workflowfunction_form',
            array('workflowFunctionId' => $mixed->getId())
        ));

        return $facade;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'workflowfunction';
    }
}
