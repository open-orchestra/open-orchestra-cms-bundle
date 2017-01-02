<?php

namespace OpenOrchestra\WorkflowAdminBundle\Facade;

use OpenOrchestra\BaseApi\Facade\FacadeInterface;
use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\ApiBundle\Facade\PaginateCollectionFacade;

/**
 * Class WorkflowProfileCollectionFacade
 */
class WorkflowProfileCollectionFacade extends PaginateCollectionFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'workflow_profiles';

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowAdminBundle\Facade\WorkflowProfileFacade>")
     */
    protected $workflowProfiles = array();

    /**
     * @param FacadeInterface $facade
     */
    public function addWorkflowProfile(FacadeInterface $facade)
    {
        $this->workflowProfiles[] = $facade;
    }

    /**
     * @return array
     */
    public function getWorkflowProfiles()
    {
        return $this->workflowProfiles;
    }
}
