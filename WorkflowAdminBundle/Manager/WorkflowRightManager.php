<?php

namespace OpenOrchestra\WorkflowAdminBundle\Manager;

use OpenOrchestra\Workflow\Model\WorkflowRightInterface;
use OpenOrchestra\Workflow\Manager\AuthorizationWorkflowRightManager;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\Workflow\Repository\WorkflowRightRepositoryInterface;

/**
 * Class WorkflowRightManager
 */
class WorkflowRightManager
{
    protected $contentTypeRepository;
    protected $workflowRightRepository;
    protected $authorizationWorkflowRightManager;
    protected $workflowRightClass;
    protected $referenceClass;

    /**
     * Constructor
     *
     * @param ContentTypeRepositoryInterface    $contentTypeRepository
     * @param WorkflowRightRepositoryInterface  $workflowRightRepository
     * @param AuthorizationWorkflowRightManager $authorizationWorkflowRightManager
     * @param string                            $workflowRightClass
     * @param string                            $referenceClass
     */
    public function __construct(
        ContentTypeRepositoryInterface $contentTypeRepository,
        WorkflowRightRepositoryInterface $workflowRightRepository,
        AuthorizationWorkflowRightManager $authorizationWorkflowRightManager,
        $workflowRightClass,
        $referenceClass
    )
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->workflowRightRepository = $workflowRightRepository;
        $this->authorizationWorkflowRightManager = $authorizationWorkflowRightManager;
        $this->workflowRightClass = $workflowRightClass;
        $this->referenceClass = $referenceClass;
    }

    /**
     * @param string $userId
     *
     * @return WorkflowRightInterface
     */
    public function loadOrGenerateByUser($userId)
    {
        $referenceClass = $this->referenceClass;
        $reference = new $referenceClass();
        $reference->setId(WorkflowRightInterface::NODE);

        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();
        foreach ($contentTypes as $key => $contentType) {
            if (!$contentType->isDefiningStatusable()) {
                unset($contentTypes[$key]);
            }
        }
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
