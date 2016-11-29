<?php

namespace OpenOrchestra\Workflow\Model;

use Doctrine\Common\Collections\Collection;

/**
 * Interface AuthorizationInterface
 */
interface AuthorizationInterface
{
    /**
     * @param string $referenceId
     */
    public function setReferenceId($referenceId);

    /**
     * @return string
     */
    public function getReferenceId();

    /**
     * @param Collection $workflowFunctions
     */
    public function setWorkflowFunctions(Collection $workflowFunctions);

    /**
     * @return Collection
     */
    public function getWorkflowFunctions();

    /**
     * @param boolean $owner
     */
    public function setOwner($owner);

    /**
     * @return boolean
     */
    public function isOwner();
}
