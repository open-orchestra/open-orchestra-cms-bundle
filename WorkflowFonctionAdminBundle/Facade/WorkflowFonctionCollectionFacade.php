<?php

namespace OpenOrchestra\WorkflowFonctionAdminBundle\Facade;

use JMS\Serializer\Annotation as Serializer;
use OpenOrchestra\BaseApi\Facade\AbstractFacade;
use OpenOrchestra\BaseApi\Facade\FacadeInterface;

/**
 * Class WorkflowFonctionCollectionFacade
 */
class WorkflowFonctionCollectionFacade extends AbstractFacade
{
    /**
     * @Serializer\Type("string")
     */
    public $collectionName = 'workflow_fonctions';

    /**
     * @Serializer\Type("array<OpenOrchestra\WorkflowFonctionAdminBundle\Facade\WorkflowFonctionFacade>")
     */
    public $workflowFonctions = array();

    /**
     * @param FacadeInterface $log
     */
    public function addWorkflowFonction(FacadeInterface $workflowFonction)
    {
        $this->workflowFonctions[] = $workflowFonction;
    }

    /**
     * @return mixed
     */
    public function getWorkflowFonctions()
    {
        return $this->workflowFonctions;
    }
}
