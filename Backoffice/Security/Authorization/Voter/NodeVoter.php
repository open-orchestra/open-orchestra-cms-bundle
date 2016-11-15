<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Security\ContributionAction;

/**
 * Class NodeVoter
 */
class NodeVoter extends AbstractVoter
{
    /**
     * @return array
     */
    protected function getSupportedClasses()
    {
        return array('OpenOrchestra\ModelInterface\Model\NodeInterface');
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            ContributionAction::ADD,
            ContributionAction::EDIT,
            ContributionAction::DELETE
        );
    }

    /**
     * @param string         $attribute
     * @param mixed          $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if ($this->isSuperAdmin($user)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        foreach ($user->getGroups() as $group) {

            if ($group->hasRole($attribute)) {
                $nodePerimeter = $group->getPerimeter(NodeInterface::ENTITY_TYPE);

                if ($nodePerimeter instanceof PerimeterInterface && $nodePerimeter->contains($object->getPath())) {

                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
