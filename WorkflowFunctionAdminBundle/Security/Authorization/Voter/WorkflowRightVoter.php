<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;
use OpenOrchestra\ModelInterface\Model\StatusableInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowRightRepositoryInterface;
use OpenOrchestra\WorkflowFunction\Model\WorkflowRightInterface;
use FOS\UserBundle\Model\UserInterface;


/**
 * Class WorkflowRightVoter
 */
class WorkflowRightVoter implements VoterInterface
{
    protected $workflowRightRepository;
    protected $contentTypeRepository;

    /**
     * @param WorkflowRightRepositoryInterface $workflowRightRepository
     * @param ContentTypeRepositoryInterface   $contentTypeRepository
     */
    public function __construct(WorkflowRightRepositoryInterface $workflowRightRepository, ContentTypeRepositoryInterface $contentTypeRepository)
    {
        $this->workflowRightRepository = $workflowRightRepository;
        $this->contentTypeRepository = $contentTypeRepository;
    }

    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return is_string($attribute);
    }

    /**
     * Checks if the voter supports the given class.
     *
     * @param string $class A class name
     *
     * @return bool true if this Voter can process the class
     */
    public function supportsClass($class)
    {
        return $class instanceof StatusableInterface;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token      A TokenInterface instance
     * @param object|null    $object     The object to secure
     * @param array          $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass($object)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }
        if (($user = $token->getUser()) instanceof UserInterface) {
            $workflowRight = $this->workflowRightRepository->findOneByUserId($token->getUser()->getId());
            if (null === $workflowRight) {
                return VoterInterface::ACCESS_DENIED;
            }

            $referenceId = WorkflowRightInterface::NODE;
            if ($object instanceof ContentInterface) {
                $contentType = $this->contentTypeRepository->findOneByContentTypeIdAndVersion($object->getContentType());
                $referenceId = $contentType->getId();
            }

            $authorizations = $workflowRight->getAuthorizations();
            foreach($authorizations as $authorization){
                if ($authorization->getReferenceId() == $referenceId) {
                    $workflowFunctions = $authorization->getWorkflowFunctions();
                    foreach($workflowFunctions as $workflowFunction){
                        if (in_array($workflowFunction->getId(), $attributes)) {
                            return VoterInterface::ACCESS_GRANTED;
                        }
                    }
                }
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
