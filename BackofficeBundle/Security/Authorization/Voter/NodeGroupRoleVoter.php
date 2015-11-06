<?php

namespace OpenOrchestra\BackofficeBundle\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use OpenOrchestra\BackofficeBundle\Model\GroupInterface;
use OpenOrchestra\BackofficeBundle\Model\NodeGroupRoleInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class NodeGroupRoleVoter
 */
class NodeGroupRoleVoter implements VoterInterface
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
        return (bool) preg_match('/^ROLE_ACCESS_[^_]+_NODE$/', $attribute);
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
        return is_subclass_of($class, 'OpenOrchestra\ModelInterface\Model\NodeInterface');
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface $token A TokenInterface instance
     * @param NodeInterface|null $object The object to secure
     * @param array $attributes An array of attributes associated with the method being invoked
     *
     * @return int either ACCESS_GRANTED, ACCESS_ABSTAIN, or ACCESS_DENIED
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass($object)) {
            return self::ACCESS_ABSTAIN;
        }
        if (($user = $token->getUser()) instanceof UserInterface && $user->isSuperAdmin()) {
            return VoterInterface::ACCESS_GRANTED;
        }

        /** @var GroupInterface $group */
        foreach ($user->getGroups() as $group) {
            if ($group->getSite()->getSiteId() != $object->getSiteId()) {
                continue;
            }
            foreach ($attributes as $attribute) {
                if (!$this->supportsAttribute($attribute)) {
                    continue;
                }
                if (
                    ($nodeGroupRole = $group->getNodeRoleByNodeAndRole($object->getNodeId(), $attribute)) instanceof NodeGroupRoleInterface
                    && $nodeGroupRole->isGranted()
                ) {
                    return self::ACCESS_GRANTED;
                }
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
