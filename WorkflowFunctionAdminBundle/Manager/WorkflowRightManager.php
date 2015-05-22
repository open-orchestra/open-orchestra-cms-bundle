<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Manager;

use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\WorkflowFunctionBundle\Manager\AuthorizationWorkflowRightManager;
use OpenOrchestra\WorkflowFunctionBundle\Document\Reference;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowRightRepositoryInterface;

/**
 * Class WorkflowRightManager
 */
class WorkflowRightManager
{

    protected $contentTypeRepository;

    protected $workflowRightRepository;

    protected $authorizationWorkflowRightManager;

    protected $workflowRightClass;

    /**
     * Constructor
     *
     * @param ContentTypeRepositoryInterface    $contentTypeRepository
     * @param AuthorizationWorkflowRightManager $authorizationWorkflowRightManager
     * @param string                            $workflowRightClass
     */
    public function __construct(ContentTypeRepositoryInterface $contentTypeRepository, WorkflowRightRepositoryInterface $workflowRightRepository, AuthorizationWorkflowRightManager $authorizationWorkflowRightManager, $workflowRightClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->workflowRightRepository = $workflowRightRepository;
        $this->authorizationWorkflowRightManager = $authorizationWorkflowRightManager;
        $this->workflowRightClass = $workflowRightClass;
    }

    /**
     * @param string $userId
     *
     * @return WorkflowRightInterface
     */
    public function loadOrGenerateByUser($userId)
    {
        $contentTypes = $this->contentTypeRepository->findAllByDeletedInLastVersion();
        $reference = new Reference();
        $reference->setId(WorkflowRightInterface::NODE);
        $contentTypes[] = $reference;

        $workflowRight = $this->workflowRightRepository->findOneByUserId($userId);
        if (null === $workflowRight) {
            $workflowRightClass = $this->workflowRightClass;
            $workflowRight = new $workflowRightClass();
            $workflowRight->setUserId($userId);
        }

        return $this->authorizationWorkflowRightManager->cleanAuthorization($contentTypes, $workflowRight);
    }
}
