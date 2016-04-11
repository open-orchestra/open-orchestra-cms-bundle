<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\TreeNodesPanelStrategy;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class DeleteNodeVoter
 */
class DeleteNodeVoter implements VoterInterface
{
    /**
     * Checks if the voter supports the given attribute.
     *
     * @param string $attribute An attribute
     *
     * @return bool true if this Voter supports the attribute, false otherwise
     */
    public function supportsAttribute($attribute)
    {
        return TreeNodesPanelStrategy::ROLE_ACCESS_DELETE_NODE === $attribute;
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
        return $class instanceof NodeInterface;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface              $token      A TokenInterface instance
     * @param NodeInterface|object|null   $object     The object to secure
     * @param array                       $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        foreach ($attributes as $attribute) {
            if (!$this->supportsAttribute($attribute)) {
                return self::ACCESS_ABSTAIN;
            }
        }

        if (!$this->supportsClass($object)) {
            return self::ACCESS_ABSTAIN;
        }

        if (NodeInterface::ROOT_NODE_ID === $object->getNodeId()) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }
}
