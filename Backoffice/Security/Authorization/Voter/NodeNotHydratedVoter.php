<?php

namespace OpenOrchestra\Backoffice\Security\Authorization\Voter;

use OpenOrchestra\UserBundle\Model\UserInterface;

/**
 * Class NodeNotHydratedVoter
 *
 * Voter checking rights on node not hydrated management
 */
class NodeNotHydratedVoter extends AbstractNodeVoter
{
    /**
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supportSubject($subject)
    {
        return (is_array($subject) &&
            array_key_exists('nodeId', $subject) &&
            array_key_exists('path', $subject)
        );
    }

    /**
     * @param mixed         $subject
     * @param UserInterface $user
     *
     * @return string
     */
    protected function isCreator($subject, UserInterface $user)
    {
        return array_key_exists('createdBy', $subject) &&
               $subject['createdBy'] === $user->getUsername();
    }

    /**
     * @param array $node
     *
     * @return string
     */
    protected function getPath($node)
    {
        return $node['path'];
    }
}
