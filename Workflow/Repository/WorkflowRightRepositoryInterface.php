<?php

namespace OpenOrchestra\Workflow\Repository;

use OpenOrchestra\Workflow\Model\WorkflowFunctionInterface;

/**
 * Interface WorkflowRightRepositoryInterface
 */
interface WorkflowRightRepositoryInterface
{
    /**
     * @param string $userId
     *
     * @return  \OpenOrchestra\Workflow\Model\WorkflowRightInterface
     */
    public function findOneByUserId($userId);

    /**
     * @param string $id
     *
     * @return mixed
     */
    public function find($id);

    /**
     * @param WorkflowFunctionInterface $workflowFunction
     *
     * @return bool
     */
    public function hasElementWithWorkflowFunction(WorkflowFunctionInterface $workflowFunction);
}
