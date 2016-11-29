<?php

namespace OpenOrchestra\Workflow\Model;

use OpenOrchestra\ModelInterface\Model\StatusInterface;

/**
 * Interface WorkflowTransitionInterface
 */
interface WorkflowTransitionInterface
{
    /**
     * @param string $status
     */
    public function setStatusFrom(StatusInterface $status);

    /**
     * @param string $status
     */
    public function setStatusTo(StatusInterface $status);
}
