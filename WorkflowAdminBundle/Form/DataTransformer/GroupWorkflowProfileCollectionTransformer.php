<?php

namespace OpenOrchestra\WorkflowAdminBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use OpenOrchestra\ModelInterface\Repository\WorkflowProfileRepositoryInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\BaseBundle\Context\CurrentSiteIdInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\ModelBundle\Document\WorkflowProfileCollection;

/**
 * Class WorkflowProfileCollectionTransformer
 */
class GroupWorkflowProfileCollectionTransformer implements DataTransformerInterface
{
    protected $workflowProfileRepository;
    protected $contentTypeRepository;
    protected $contextManager;

    /**
     * @param WorkflowProfileRepositoryInterface $workflowProfileRepository
     * @param ContentTypeRepositoryInterface     $contentTypeRepository
     * @param CurrentSiteIdInterface             $contextManager
     */
    public function __construct(
        WorkflowProfileRepositoryInterface $workflowProfileRepository,
        ContentTypeRepositoryInterface $contentTypeRepository,
        CurrentSiteIdInterface $contextManager
    ) {
        $this->workflowProfileRepository = $workflowProfileRepository;
        $this->contentTypeRepository = $contentTypeRepository;
        $this->contextManager = $contextManager;
    }

    /**
     * Transform an array of roles to choices
     *
     * @param Collection $value
     *
     * @return array
     */
    public function transform($value)
    {
        $result = array();
        $workflowProfiles = $this->workflowProfileRepository->findAll();
        $contentTypes = $this->contentTypeRepository->findAllNotDeletedInLastVersion();

        $settedWorkflowProfiles = array();
        if ($value instanceof Collection) {
            foreach ($value as $workflowProfileCollectionName => $workflowProfileCollection) {
                $settedWorkflowProfiles[$workflowProfileCollectionName] = array();
                foreach ($workflowProfileCollection->getProfiles() as $workflowProfile) {
                    $settedWorkflowProfiles[$workflowProfileCollectionName][] = $workflowProfile->getId();
                }
            }
        }
        foreach ($workflowProfiles as $workflowProfile) {
            $nodeTypeId = NodeInterface::ENTITY_TYPE;
            $result[$nodeTypeId][$workflowProfile->getId()] = array_key_exists($nodeTypeId, $settedWorkflowProfiles) && in_array($workflowProfile->getId(), $settedWorkflowProfiles[$nodeTypeId]);
        }

        foreach ($contentTypes as $contentType) {
            $contentTypeId = $contentType->getContentTypeId();
            foreach ($workflowProfiles as $workflowProfile) {
                $result[$contentTypeId][$workflowProfile->getId()] = array_key_exists($contentTypeId, $settedWorkflowProfiles) && in_array($workflowProfile->getId(), $settedWorkflowProfiles[$contentTypeId]);
            }
        }

        return $result;
    }

    /**
     * Transform an array choices to array of roles
     *
     * @param array $value
     *
     * @return Collection
     */
    public function reverseTransform($value)
    {
        $result = new ArrayCollection();
        if (is_array($value)) {
            foreach ($value as $key => $profiles) {
                if (is_array($profiles)) {
                    $profileCollection = new WorkflowProfileCollection();
                    foreach ($profiles as $profileId => $checked) {
                        if ($checked) {
                            $document = $this->workflowProfileRepository->find($profileId);
                            if (!is_null($document)) {
                                $profileCollection->addProfile($document);
                            }
                        }
                    }
                }
                $result->set($key, $profileCollection);
            }
        }

        return $result;
    }
}
