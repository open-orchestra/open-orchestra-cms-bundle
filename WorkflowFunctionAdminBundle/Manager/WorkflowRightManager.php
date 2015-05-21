<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use OpenOrchestra\ModelBundle\Repository\ContentTypeRepository;
use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;

/**
 * Class WorkflowRightManager
 */
class WorkflowRightManager
{
    protected $documentManager;

    /**
     * Constructor
     *
     * @param ContentTypeRepository $contentTypeRepository
     * @param string                $authorizationClass
     * @param string                $workflowRightClass
     */
    public function __construct(ContentTypeRepository $contentTypeRepository, $authorizationClass, $workflowRightClass)
    {
        $this->contentTypeRepository = $contentTypeRepository;
        $this->authorizationClass = $authorizationClass;
        $this->workflowRightClass = $workflowRightClass;
    }

    /**
     * @param WorkflowRightInterface|null $workflowRight
     *
     * @return WorkflowRightInterface
     */
    public function clean($workflowRight = null)
    {
        if (null === $workflowRight) {
            $workflowRightClass = $this->workflowRightClass;
            $workflowRight = new $workflowRightClass();
        }
        $contentTypes = $this->contentTypeRepository->findAllByDeletedInLastVersion();
        $contentTypes[WorkflowRightInterface::NODE] = null;

        $authorizations = $workflowRight->getAuthorizations();
        foreach ($authorizations as $authorization) {
            if (!array_key_exists($authorization->getName(), $contentTypes)) {
                $workflowRight->removeAuthorization($authorization);
                continue;
            }
            unset($contentTypes[$authorization->getName()]);
        }

        $authorizationClass = $this->authorizationClass;
        foreach($contentTypes as $key => $contentType){
            $authorization = new $authorizationClass();
            $authorization->setName($key);
            $workflowRight->addAuthorization($authorization);
        }

        return $workflowRight;
    }
}
