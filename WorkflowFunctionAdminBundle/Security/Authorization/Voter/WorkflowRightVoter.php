<?php

namespace OpenOrchestra\WorkflowFunctionAdminBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\ModelInterface\Model\ContentInterface;
use OpenOrchestra\WorkflowFunction\Repository\WorkflowRightRepositoryInterface;
use FOS\UserBundle\Model\UserInterface;
use OpenOrchestra\ModelInterface\Repository\ContentTypeRepositoryInterface;

/**
 * Class WorkflowRightVoter
 */
class WorkflowRightVoter implements VoterInterface
{
    protected $workflowRightRepository;

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
        return 0 === strpos($attribute, 'ROLE_ACCESS');
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
        return $class instanceof ContentInterface;
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
            $contentType = $this->contentTypeRepository->findOneByContentTypeIdAndVersion($object->getContentType());
            if (null === $workflowRight) {

                return VoterInterface::ACCESS_DENIED;
            }
            $authorizations = $workflowRight->getAuthorizations();
            foreach($authorizations as $authorization){
                if ($authorization->getReferenceId() == $contentType->getId()) {
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
