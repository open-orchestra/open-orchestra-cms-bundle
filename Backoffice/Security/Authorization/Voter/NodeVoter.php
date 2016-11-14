<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use OpenOrchestra\ModelInterface\Model\NodeInterface;
use OpenOrchestra\Backoffice\Security\NodeRole;
use OpenOrchestra\Backoffice\Model\PerimeterInterface;

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
        return array('NodeInterface');
    }

    /**
     * @return array
     */
    protected function getSupportedAttributes()
    {
        return array(
            NodeRole::READER,
            NodeRole::CREATOR,
            NodeRole::EDITOR,
            NodeRole::SUPPRESSOR
        );
    }

    /**
     * @param string               $attribute
     * @param object               $object
     * @param UserInterface|string $user
     *
     * @return bool
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        if (is_null($user)) {
            return VoterInterface::ACCESS_DENIED;
        }

        if ($this->isSuperAdmin($user)) {
            return VoterInterface::ACCESS_GRANTED;
        }

        foreach ($user->getGroups() as $group) {

            if ($group->hasRole($attribute)) {
                $nodePerimeter = $group->getPerimeters(NodeInterface::ENTITY_TYPE);

                if ($nodePerimeter instanceof PerimeterInterface && $nodePerimeter->contains($object->getPath())) {

                    return VoterInterface::ACCESS_GRANTED;
                }
            }
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
