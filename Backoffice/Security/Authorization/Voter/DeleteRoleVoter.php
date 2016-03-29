<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\Backoffice\NavigationPanel\Strategies\AdministrationPanelStrategy;
use OpenOrchestra\Backoffice\UsageFinder\RoleUsageFinder;
use OpenOrchestra\ModelInterface\Model\RoleInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

/**
 * Class DeleteRoleVoter
 */
class DeleteRoleVoter implements VoterInterface
{
    protected $usageFinder;

    /**
     * @param RoleUsageFinder $usageFinder
     */
    public function __construct(RoleUsageFinder $usageFinder)
    {
        $this->usageFinder = $usageFinder;
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
        return AdministrationPanelStrategy::ROLE_ACCESS_DELETE_ROLE == $attribute;
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
        return $class instanceof RoleInterface;
    }

    /**
     * Returns the vote for the given parameters.
     *
     * This method must return one of the following constants:
     * ACCESS_GRANTED, ACCESS_DENIED, or ACCESS_ABSTAIN.
     *
     * @param TokenInterface              $token      A TokenInterface instance
     * @param RoleInterface|object|null   $object     The object to secure
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

        if ($this->usageFinder->hasUsage($object)) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_GRANTED;
    }
}
