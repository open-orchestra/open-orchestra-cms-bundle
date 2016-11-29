<?php

namespace OpenOrchestra\Workflow\Model;

/**
 * Interface WorkflowProfileCollectionInterface
 */
interface WorkflowProfileCollectionInterface
{
    /**
     * @param WorkflowProfileInterface $profile
     */
    public function addProfile(WorkflowProfileInterface $profile);
}
